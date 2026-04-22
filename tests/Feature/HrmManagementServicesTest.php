<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\Department;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeProfile;
use App\Models\FeedbackMessage;
use App\Models\LeaveBalanceTransaction;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\SalaryAdjustment;
use App\Models\SalaryHistory;
use App\Services\DepartmentApprovalService;
use App\Services\LeaveManagementService;
use App\Services\UserApprovalService;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\Support\CreatesHrmUsers;

class HrmManagementServicesTest extends TestCase
{
    use RefreshDatabase;
    use CreatesHrmUsers;

    public function test_user_approval_service_approves_salary_change_and_records_history(): void
    {
        $requester = $this->makeHrmUser('Salary Requester', [Capability::MANAGE_SALARY], 3);
        $approver = $this->makeHrmUser('Salary Approver', [Capability::APPROVE_REQUESTS, Capability::MANAGE_SALARY], 5);
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
    }

    public function test_department_approval_service_approves_create_and_rejects_duplicate_name(): void
    {
        $requester = $this->makeHrmUser('Department Requester', [Capability::MANAGE_DEPARTMENTS], 3);
        $approver = $this->makeHrmUser('Department Approver', [Capability::APPROVE_REQUESTS, Capability::MANAGE_DEPARTMENTS], 5);

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
