<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\ApprovalDecisionDelivery;
use App\Models\Department;
use App\Models\EmailLog;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeProfile;
use App\Models\FeedbackMessage;
use App\Models\LeaveBalanceTransaction;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\SalaryAdjustment;
use App\Models\SalaryHistory;
use App\Models\User;
use App\Mail\ApprovalDecisionMail;
use App\Services\DepartmentApprovalService;
use App\Services\LeaveManagementService;
use App\Services\ApprovalDecisionNotifier;
use App\Services\UserApprovalService;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendApprovalDecisionEmailJob;
use Tests\TestCase;
use Tests\Support\CreatesHrmUsers;

class HrmManagementServicesTest extends TestCase
{
    use RefreshDatabase;
    use CreatesHrmUsers;

    public function test_user_approval_service_approves_salary_change_and_records_history(): void
    {
        Queue::fake();

        $requester = $this->makeHrmUser('Salary Requester', [Capability::MANAGE_SALARY], 3);
        $approver = $this->makeHrmUser('Salary Approver', [Capability::APPROVE_SALARY_REQUESTS, Capability::MANAGE_SALARY], 5);
        $target = $this->makeHrmUser('Salary Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 10000000,
        ]);

        $this->actingAs($requester);
        $request = app(UserApprovalService::class)->submitSalaryChangeRequest($target, 12500000, 'Annual review');

        $this->assertSame('pending', $request->status);
        $this->assertDatabaseHas('approval_request_changes', [
            'approval_request_id' => $request->id,
            'field_name' => 'new_salary',
            'old_value' => json_encode(10000000.0),
            'new_value' => json_encode(12500000.0),
        ]);

        $this->actingAs($approver);
        app(UserApprovalService::class)->approve($request->fresh('changes'), 'Approved raise');

        $target->employeeProfile->refresh();
        $request->refresh();

        $this->assertSame('approved', $request->status);
        $this->assertSame($approver->id, (int) $request->reviewed_by);
        $this->assertSame(12500000.0, (float) $target->employeeProfile->base_salary);
        $this->assertDatabaseHas('salary_histories', [
            'employee_profile_id' => $target->employeeProfile->id,
            'old_salary' => 10000000,
            'new_salary' => 12500000,
            'approved_by' => $approver->id,
            'note' => 'Approved raise',
        ]);
        $this->assertDatabaseHas('approval_decision_deliveries', [
            'module' => 'salary',
            'reference_type' => ApprovalRequest::class,
            'reference_id' => $request->id,
            'recipient_user_id' => $requester->id,
            'decision' => 'approved',
            'status' => 'queued',
        ]);
    }

    public function test_salary_update_endpoint_updates_salary_and_records_history_for_salary_approver(): void
    {
        Queue::fake();

        $approver = $this->makeHrmUser('Salary Direct Approver', [
            Capability::MANAGE_EMPLOYEES,
            Capability::MANAGE_SALARY,
            Capability::APPROVE_SALARY_REQUESTS,
        ], 5);
        $target = $this->makeHrmUser('Salary Direct Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 10000000,
        ]);

        $this->actingAs($approver)
            ->put(route('web.users.salary', $target), [
                'base_salary' => 13500000,
                'reason' => 'Direct raise',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $target->employeeProfile->refresh();

        $this->assertSame(13500000.0, (float) $target->employeeProfile->base_salary);
        $this->assertDatabaseHas('salary_histories', [
            'employee_profile_id' => $target->employeeProfile->id,
            'old_salary' => 10000000,
            'new_salary' => 13500000,
            'approved_by' => $approver->id,
            'note' => 'Direct raise',
        ]);
        $this->assertDatabaseHas('approval_decision_deliveries', [
            'module' => 'salary',
            'reference_type' => User::class,
            'reference_id' => $target->id,
            'recipient_user_id' => $target->id,
            'decision' => 'approved',
            'status' => 'queued',
        ]);
    }

    public function test_salary_update_endpoint_submits_approval_when_actor_cannot_directly_approve_salary(): void
    {
        $requester = $this->makeHrmUser('Salary Direct Requester', [
            Capability::MANAGE_EMPLOYEES,
            Capability::MANAGE_SALARY,
        ], 3);
        $target = $this->makeHrmUser('Salary Pending Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 9000000,
        ]);

        $this->actingAs($requester)
            ->put(route('web.users.salary', $target), [
                'base_salary' => 12000000,
                'reason' => 'Pending raise',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $target->employeeProfile->refresh();

        $this->assertSame(9000000.0, (float) $target->employeeProfile->base_salary);
        $this->assertDatabaseHas('approval_requests', [
            'request_type' => 'user_salary_change',
            'target_type' => \App\Models\User::class,
            'target_id' => $target->id,
            'requested_by' => $requester->id,
            'status' => 'pending',
            'reason' => 'Pending raise',
        ]);
        $this->assertDatabaseCount('salary_histories', 0);
    }

    public function test_general_user_update_route_ignores_base_salary_changes(): void
    {
        $manager = $this->makeHrmUser('General Update Manager', [Capability::MANAGE_EMPLOYEES], 5);
        $target = $this->makeHrmUser('General Update Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 11000000,
        ]);

        $this->actingAs($manager)
            ->put(route('web.users.update', $target), [
                'name' => 'General Update Target Edited',
                'email' => 'general.update.target@gmail.com',
                'phone' => '0912345678',
                'hire_date' => '2026-01-01',
                'department_id' => $target->employeeProfile->department_id,
                'position_id' => $target->employeeProfile->position_id,
                'employment_status' => 'active',
                'employment_type' => 'official',
                'status' => 'active',
                'base_salary' => 20000000,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $target->refresh();
        $target->employeeProfile->refresh();

        $this->assertSame('General Update Target Edited', $target->name);
        $this->assertSame(11000000.0, (float) $target->employeeProfile->base_salary);
        $this->assertDatabaseMissing('approval_requests', [
            'request_type' => 'user_salary_change',
            'target_id' => $target->id,
            'requested_by' => $manager->id,
        ]);
    }

    public function test_department_approval_service_approves_create_and_rejects_duplicate_name(): void
    {
        Queue::fake();

        $requester = $this->makeHrmUser('Department Requester', [Capability::MANAGE_DEPARTMENTS], 3);
        $approver = $this->makeHrmUser('Department Approver', [Capability::APPROVE_DEPARTMENT_REQUESTS, Capability::MANAGE_DEPARTMENTS], 5);

        $this->actingAs($requester);
        $request = app(DepartmentApprovalService::class)->submitCreateRequest([
            'name' => 'Research Lab',
            'description' => 'Internal research team',
            'manager_user_id' => $requester->id,
            'is_active' => true,
        ]);

        $this->actingAs($approver);
        $department = app(DepartmentApprovalService::class)->approve($request->fresh('changes'), 'Looks good');

        $this->assertSame('Research Lab', $department->name);
        $this->assertTrue((bool) $department->is_active);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'manager_user_id' => $requester->id,
        ]);
        $this->assertDatabaseHas('approval_decision_deliveries', [
            'module' => 'department',
            'reference_type' => ApprovalRequest::class,
            'reference_id' => $request->id,
            'recipient_user_id' => $requester->id,
            'decision' => 'approved',
            'status' => 'queued',
        ]);

        $this->actingAs($requester);
        $duplicateRequest = app(DepartmentApprovalService::class)->submitCreateRequest([
            'name' => 'Research Lab',
            'description' => 'Duplicate name',
            'manager_user_id' => null,
            'is_active' => true,
        ]);

        $this->actingAs($approver);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(DepartmentApprovalService::class)->approve($duplicateRequest->fresh('changes'));
    }

    public function test_approval_decision_notifier_deduplicates_identical_approval_events(): void
    {
        Queue::fake();

        $requester = $this->makeHrmUser('Notifier Requester', [Capability::MANAGE_SALARY], 3);
        $target = $this->makeHrmUser('Notifier Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 8000000,
        ]);

        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'user_salary_change',
            'target_type' => User::class,
            'target_id' => $target->id,
            'requested_by' => $requester->id,
            'status' => 'approved',
            'submitted_at' => now(),
            'reviewed_at' => now(),
            'reason' => 'Dedupe test',
        ]);

        $notifier = app(ApprovalDecisionNotifier::class);

        $first = $notifier->notifyApprovalRequestDecision($approvalRequest->fresh(['requester']), 'approved', '/users/employee-requests', 'Approved once');
        $second = $notifier->notifyApprovalRequestDecision($approvalRequest->fresh(['requester']), 'approved', '/users/employee-requests', 'Approved once');

        $this->assertNotNull($first);
        $this->assertSame($first?->id, $second?->id);
        $this->assertSame(1, ApprovalDecisionDelivery::query()->count());
    }

    public function test_approval_decision_email_dispatch_waits_for_commit_and_skips_on_rollback(): void
    {
        $requester = $this->makeHrmUser('Rollback Requester', [Capability::MANAGE_SALARY], 3);
        $approver = $this->makeHrmUser('Rollback Approver', [Capability::APPROVE_SALARY_REQUESTS, Capability::MANAGE_SALARY], 5);
        $target = $this->makeHrmUser('Rollback Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 7000000,
        ]);

        $this->actingAs($requester);
        $request = app(UserApprovalService::class)->submitSalaryChangeRequest($target, 9500000, 'Rollback queue test');

        $this->actingAs($approver);

        try {
            DB::transaction(function () use ($request) {
                app(UserApprovalService::class)->approve($request->fresh('changes'), 'Rollback after approval');

                throw new \RuntimeException('force rollback');
            });
        } catch (\RuntimeException $exception) {
            $this->assertSame('force rollback', $exception->getMessage());
        }

        $request->refresh();
        $target->employeeProfile->refresh();

        $this->assertSame('pending', $request->status);
        $this->assertSame(7000000.0, (float) $target->employeeProfile->base_salary);
        $this->assertDatabaseMissing('approval_decision_deliveries', [
            'reference_type' => ApprovalRequest::class,
            'reference_id' => $request->id,
        ]);
    }

    public function test_send_approval_decision_email_job_marks_failure_and_logs_error(): void
    {
        $recipient = $this->makeHrmUser('Mail Failure Target', [Capability::VIEW_OWN_SALARY], 1);

        $delivery = ApprovalDecisionDelivery::query()->create([
            'dedupe_key' => sha1('mail-failure-test'),
            'module' => 'salary',
            'channel' => 'mail',
            'decision' => 'approved',
            'status' => 'queued',
            'recipient_user_id' => $recipient->id,
            'recipient_email' => $recipient->email,
            'subject' => 'Mail failure test',
            'action_url' => '/my-salary',
            'reference_type' => User::class,
            'reference_id' => $recipient->id,
            'payload' => [
                'recipient_name' => $recipient->name,
                'item_label' => 'Cap nhat luong',
                'reviewer_name' => 'System',
                'decision_at' => now()->format('d/m/Y H:i'),
            ],
            'queued_at' => now(),
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with($recipient->email)
            ->andReturn(new class {
                public function send($mailable): void
                {
                    throw new \RuntimeException('SMTP offline');
                }
            });

        $job = new SendApprovalDecisionEmailJob($delivery->id);

        try {
            $job->handle();
            $this->fail('The mail job should have thrown an exception.');
        } catch (\RuntimeException $exception) {
            $job->failed($exception);
        }

        $delivery->refresh();

        $this->assertSame('failed', $delivery->status->value);
        $this->assertSame(1, (int) $delivery->attempt_count);
        $this->assertStringContainsString('SMTP offline', (string) $delivery->last_error);
        $this->assertDatabaseHas('email_logs', [
            'receiver_email' => $recipient->email,
            'subject' => 'Mail failure test',
            'status' => 'failed',
        ]);
        $this->assertSame(1, EmailLog::query()->where('status', 'failed')->count());
    }

    public function test_approval_decision_notifier_skips_module_when_feature_flag_is_disabled(): void
    {
        Queue::fake();
        config([
            'approval_notifications.enabled' => true,
            'approval_notifications.in_app_enabled' => true,
            'approval_notifications.mail.enabled' => true,
            'approval_notifications.modules.user' => false,
        ]);

        $requester = $this->makeHrmUser('Flag Off Requester', [Capability::MANAGE_EMPLOYEES], 3);
        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'user_create',
            'target_type' => User::class,
            'target_id' => 0,
            'requested_by' => $requester->id,
            'status' => 'approved',
            'submitted_at' => now(),
            'reviewed_at' => now(),
            'reason' => 'Feature flag off',
        ]);

        $delivery = app(ApprovalDecisionNotifier::class)->notifyApprovalRequestDecision(
            $approvalRequest->fresh(['requester']),
            'approved',
            '/users/employee-requests',
            'Feature flag off'
        );

        $this->assertNotNull($delivery);
        $this->assertSame('skipped', $delivery?->status->value);
        $this->assertStringContainsString('disabled', (string) $delivery?->last_error);
        $this->assertSame(0, Notification::query()->count());
        Queue::assertNothingPushed();
    }

    public function test_send_approval_decision_email_job_marks_success_and_avoids_duplicate_resend(): void
    {
        Mail::fake();

        $recipient = $this->makeHrmUser('Mail Success Target', [Capability::VIEW_OWN_SALARY], 1);

        $delivery = ApprovalDecisionDelivery::query()->create([
            'dedupe_key' => sha1('mail-success-test'),
            'module' => 'salary',
            'channel' => 'mail',
            'decision' => 'approved',
            'status' => 'queued',
            'recipient_user_id' => $recipient->id,
            'recipient_email' => $recipient->email,
            'subject' => 'Mail success test',
            'action_url' => '/my-salary',
            'reference_type' => User::class,
            'reference_id' => $recipient->id,
            'payload' => [
                'recipient_name' => $recipient->name,
                'item_label' => 'Cap nhat luong',
                'reviewer_name' => 'System',
                'decision_at' => now()->format('d/m/Y H:i'),
            ],
            'queued_at' => now(),
        ]);

        $job = new SendApprovalDecisionEmailJob($delivery->id);
        $job->handle();
        $job->handle();

        $delivery->refresh();

        $this->assertSame('sent', $delivery->status->value);
        $this->assertNotNull($delivery->sent_at);
        $this->assertSame(1, (int) $delivery->attempt_count);
        $this->assertSame(1, EmailLog::query()->where('status', 'success')->count());
        Mail::assertSent(ApprovalDecisionMail::class, 1);
    }

    public function test_split_approval_permissions_isolate_user_department_and_salary_flows(): void
    {
        $requester = $this->makeHrmUser('Split Approval Requester', [Capability::MANAGE_EMPLOYEES, Capability::MANAGE_SALARY], 3);
        $salaryTarget = $this->makeHrmUser('Split Salary Target', [Capability::VIEW_OWN_SALARY], 1, [
            'base_salary' => 9000000,
        ]);
        $userApprover = $this->makeHrmUser('Split User Approver', [Capability::APPROVE_USER_REQUESTS], 5);
        $salaryApprover = $this->makeHrmUser('Split Salary Approver', [Capability::APPROVE_SALARY_REQUESTS], 5);
        $departmentApprover = $this->makeHrmUser('Split Department Approver', [Capability::APPROVE_DEPARTMENT_REQUESTS], 5);

        $this->actingAs($requester);
        app(UserApprovalService::class)->submitCreateRequest([
            'name' => 'Pending Employee',
            'email' => 'pending.employee@example.com',
            'phone' => '0900000001',
            'department_id' => $requester->employeeProfile->department_id,
            'position_id' => $requester->employeeProfile->position_id,
            'status' => 'active',
            'employment_status' => 'active',
            'employment_type' => 'official',
            'hire_date' => '2026-01-01',
            'base_salary' => 8000000,
        ]);
        app(UserApprovalService::class)->submitSalaryChangeRequest($salaryTarget, 12000000, 'Split approval test');

        $userApprovalPage = $this->actingAs($userApprover)->get(route('web.user-approvals.index'));
        $userApprovalPage->assertOk();
        $userApprovalPage->assertViewHas('page');
        $userPage = $userApprovalPage->viewData('page');
        $userRequestTypes = collect(data_get($userPage, 'props.approvalRequests.data', []))->pluck('request_type')->all();
        $this->assertSame(['user_create'], $userRequestTypes);

        $salaryApprovalPage = $this->actingAs($salaryApprover)->get(route('web.user-approvals.index'));
        $salaryApprovalPage->assertOk();
        $salaryApprovalPage->assertViewHas('page');
        $salaryPage = $salaryApprovalPage->viewData('page');
        $salaryRequestTypes = collect(data_get($salaryPage, 'props.approvalRequests.data', []))->pluck('request_type')->all();
        $this->assertSame(['user_salary_change'], $salaryRequestTypes);

        $this->actingAs($departmentApprover)
            ->getJson(route('web.user-approvals.index'))
            ->assertForbidden();

        $this->actingAs($userApprover)
            ->getJson(route('web.department-approvals.index'))
            ->assertForbidden();

        $this->actingAs($departmentApprover)
            ->get(route('web.department-approvals.index'))
            ->assertOk();
    }

    public function test_feedback_controller_allows_sender_and_handler_conversation_flow(): void
    {
        Mail::fake();

        $sender = $this->makeHrmUser('Feedback Sender', [Capability::CREATE_FEEDBACK, Capability::VIEW_FEEDBACKS], 1);
        $handler = $this->makeHrmUser('Feedback Handler', [Capability::VIEW_FEEDBACKS, Capability::REPLY_FEEDBACK], 2);

        $this->actingAs($sender)
            ->post(route('feedbacks.store'), [
                'receiver_position_id' => $handler->employeeProfile->position_id,
                'subject' => 'Need equipment',
                'message' => 'Please approve a new laptop.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $feedback = FeedbackMessage::query()->firstOrFail();

        $this->assertSame($sender->id, (int) $feedback->sender_id);
        $this->assertSame($handler->employeeProfile->position_id, (int) $feedback->receiver_position_id);
        $this->assertSame('waiting_handler', $feedback->conversation_status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $handler->id,
            'category' => 'feedback',
            'reference_type' => FeedbackMessage::class,
            'reference_id' => $feedback->id,
        ]);

        $this->actingAs($handler)
            ->post(route('feedbacks.reply', $feedback), [
                'reply_message' => 'Approved by manager.',
                'status' => 'read',
                'conversation_status' => 'resolved',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $feedback->refresh();

        $this->assertTrue((bool) $feedback->is_replied);
        $this->assertSame('resolved', $feedback->conversation_status);
        $this->assertSame($handler->id, (int) $feedback->replied_by);
        $this->assertDatabaseHas('feedback_replies', [
            'feedback_message_id' => $feedback->id,
            'replied_by' => $handler->id,
            'message' => 'Approved by manager.',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $sender->id,
            'category' => 'feedback',
            'reference_type' => FeedbackMessage::class,
            'reference_id' => $feedback->id,
        ]);
    }

    public function test_feedback_controller_rejects_feedback_to_non_superior_position(): void
    {
        Mail::fake();

        $sender = $this->makeHrmUser('Feedback Mid Sender', [Capability::CREATE_FEEDBACK, Capability::VIEW_FEEDBACKS], 2);
        $lowerHandler = $this->makeHrmUser('Feedback Lower Handler', [Capability::VIEW_FEEDBACKS, Capability::REPLY_FEEDBACK], 1);

        $this->actingAs($sender)
            ->from(route('feedbacks.index'))
            ->post(route('feedbacks.store'), [
                'receiver_position_id' => $lowerHandler->employeeProfile->position_id,
                'subject' => 'Invalid direction',
                'message' => 'This should not be sent downward.',
            ])
            ->assertRedirect(route('feedbacks.index'))
            ->assertSessionHasErrors(['receiver_position_id']);

        $this->assertSame(0, FeedbackMessage::query()->count());
    }

    public function test_feedback_controller_forbids_view_only_user_from_replying_as_handler(): void
    {
        Mail::fake();

        $sender = $this->makeHrmUser('Feedback No Reply Sender', [Capability::CREATE_FEEDBACK, Capability::VIEW_FEEDBACKS], 1);
        $handler = $this->makeHrmUser('Feedback Real Handler', [Capability::VIEW_FEEDBACKS, Capability::REPLY_FEEDBACK], 2);
        $viewer = $this->makeHrmUser('Feedback View Only Handler', [Capability::VIEW_FEEDBACKS], 2);

        $this->actingAs($sender)
            ->post(route('feedbacks.store'), [
                'receiver_position_id' => $handler->employeeProfile->position_id,
                'subject' => 'Needs authorized handler',
                'message' => 'Only reply-capable handlers may answer.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $feedback = FeedbackMessage::query()->firstOrFail();

        $this->actingAs($viewer)
            ->post(route('feedbacks.reply', $feedback), [
                'reply_message' => 'I can view but should not reply.',
                'status' => 'read',
                'conversation_status' => 'resolved',
            ])
            ->assertForbidden();

        $feedback->refresh();
        $this->assertFalse((bool) $feedback->is_replied);
        $this->assertSame('waiting_handler', $feedback->conversation_status);
        $this->assertDatabaseMissing('feedback_replies', [
            'feedback_message_id' => $feedback->id,
            'replied_by' => $viewer->id,
        ]);
    }

    public function test_leave_management_service_grants_prorated_balance_and_adjusts_it(): void
    {
        $actor = $this->makeHrmUser('Leave Manager', [Capability::MANAGE_LEAVE_POLICY], 4);
        $employee = $this->makeHrmUser('Leave Employee', [Capability::VIEW_OWN_ATTENDANCE], 1, [
            'hire_date' => '2026-04-15',
        ]);
        $leaveType = LeaveType::query()->create([
            'code' => 'TEST_ANNUAL',
            'name' => 'Test Annual Leave',
            'is_paid' => true,
            'deducts_balance' => true,
            'requires_attachment' => false,
            'annual_quota' => 12,
            'prorate_by_hire_date' => true,
            'max_days_per_request' => null,
            'is_active' => true,
        ]);

        $service = app(LeaveManagementService::class);
        $balance = $service->grantBalance($actor, [
            'employee_profile_id' => $employee->employeeProfile->id,
            'leave_type_id' => $leaveType->id,
            'year' => 2026,
            'note' => 'Initial grant',
        ]);

        $this->assertSame(9.0, (float) $balance->fresh()->accrued_days);
        $this->assertDatabaseHas('leave_balance_transactions', [
            'employee_leave_balance_id' => $balance->id,
            'type' => 'grant',
            'created_by' => $actor->id,
        ]);

        $service->adjustBalance($actor, $balance->fresh(), [
            'days' => 1.5,
            'note' => 'Manual carry adjustment',
        ]);

        $balance->refresh();
        $this->assertSame(1.5, (float) $balance->adjusted_days);
        $this->assertSame(10.5, (float) $balance->available_days);
        $this->assertSame(2, LeaveBalanceTransaction::query()->where('employee_leave_balance_id', $balance->id)->count());
    }

    public function test_salary_controller_stores_and_deletes_manual_adjustments(): void
    {
        $manager = $this->makeHrmUser('Salary Manager', [Capability::MANAGE_SALARY, Capability::VIEW_ALL_SALARY], 5);
        $employee = $this->makeHrmUser('Salary Adjustment Employee', [Capability::VIEW_OWN_SALARY], 1);

        $this->actingAs($manager)
            ->post(route('salary.company.adjustments.store'), [
                'employee_profile_id' => $employee->employeeProfile->id,
                'month' => 4,
                'year' => 2026,
                'type' => 'allowance',
                'label' => 'Project bonus',
                'amount' => 750000,
                'note' => 'April launch',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $adjustment = SalaryAdjustment::query()->firstOrFail();

        $this->assertSame($manager->id, (int) $adjustment->created_by);
        $this->assertSame(750000.0, (float) $adjustment->amount);

        $this->actingAs($manager)
            ->delete(route('salary.company.adjustments.destroy', $adjustment))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('salary_adjustments', [
            'id' => $adjustment->id,
        ]);
    }

}
