<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Project;
use App\Models\ProjectImplementationSubtask;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProjectReminderService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function sendDeadlineReminders(int $days = 3): int
    {
        $count = 0;
        $today = now('Asia/Ho_Chi_Minh')->startOfDay();
        $maxDate = $today->copy()->addDays($days)->toDateString();

        ProjectImplementationSubtask::query()
            ->with(['project.members.employeeProfile.user', 'detail', 'assignee.user'])
            ->whereIn('status', ['planned', 'in_progress'])
            ->whereNull('deadline_reminded_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', $today->toDateString())
            ->whereDate('due_date', '<=', $maxDate)
            ->chunkById(100, function ($subtasks) use (&$count): void {
                foreach ($subtasks as $subtask) {
                    $project = $subtask->project;
                    if (!$project) {
                        continue;
                    }

                    $users = $this->projectRecipientUsers($project);
                    if ($subtask->assignee?->user) {
                        $users = $users->push($subtask->assignee->user)->unique('id')->values();
                    }

                    $this->notifyUsers(
                        $users,
                        'Công việc sắp hết hạn',
                        sprintf(
                            'Công việc "%s" trong dự án "%s" sẽ đến hạn vào %s.',
                            $subtask->title,
                            $project->name,
                            optional($subtask->due_date)->format('d/m/Y')
                        ),
                        '/my-projects',
                        ProjectImplementationSubtask::class,
                        $subtask->id,
                        [
                            'project_id' => $project->id,
                            'implementation_detail_id' => $subtask->project_implementation_detail_id,
                            'subtask_id' => $subtask->id,
                        ]
                    );

                    $subtask->update(['deadline_reminded_at' => now()]);
                    $count++;
                }
            });

        return $count;
    }

    public function sendPeriodicReports(string $period = 'weekly'): int
    {
        $count = 0;

        Project::query()
            ->with([
                'members.employeeProfile.user',
                'implementationDetails.subtasks',
                'workLogs',
            ])
            ->whereIn('status', ['planning', 'in_progress', 'on_hold'])
            ->chunkById(50, function ($projects) use (&$count, $period): void {
                foreach ($projects as $project) {
                    $activeSubtasks = $project->implementationDetails
                        ->flatMap(fn ($detail) => $detail->subtasks)
                        ->filter(fn ($subtask) => $subtask->status !== 'cancelled');
                    $completedSubtasks = $activeSubtasks->filter(fn ($subtask) => $subtask->status === 'completed')->count();
                    $totalSubtasks = $activeSubtasks->count();
                    $lateSubtasks = $activeSubtasks
                        ->filter(fn ($subtask) => $subtask->status !== 'completed' && $subtask->due_date && $subtask->due_date->isPast())
                        ->count();
                    $message = sprintf(
                        'Báo cáo %s dự án "%s": %d/%d công việc con hoàn thành, %d công việc sắp/quá hạn, %.2f giờ thực tế đã ghi.',
                        $period === 'monthly' ? 'tháng' : 'tuần',
                        $project->name,
                        $completedSubtasks,
                        $totalSubtasks,
                        $lateSubtasks,
                        (float) $project->workLogs->sum('hours')
                    );

                    $this->notifyUsers(
                        $this->projectRecipientUsers($project),
                        $period === 'monthly' ? 'Báo cáo tháng của dự án' : 'Báo cáo tuần của dự án',
                        $message,
                        '/my-projects',
                        Project::class,
                        $project->id,
                        ['project_id' => $project->id, 'period' => $period]
                    );

                    $count++;
                }
            });

        return $count;
    }

    private function projectRecipientUsers(Project $project): Collection
    {
        return ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->with('employeeProfile.user')
            ->get()
            ->map(fn (ProjectMember $member) => $member->employeeProfile?->user)
            ->filter()
            ->unique('id')
            ->values();
    }

    private function notifyUsers(Collection $users, string $title, string $message, string $url, string $referenceType, int $referenceId, array $data = []): void
    {
        $ids = $users->pluck('id')->filter()->values()->all();
        if (!empty($ids)) {
            $this->notificationService->createForUsers($ids, $title, $message, $data, $url, 'main', 'project', null, $referenceType, $referenceId);
        }

        $users->each(fn (User $user) => $this->sendEmail($user, $title, $message));
    }

    private function sendEmail(User $user, string $subject, string $body): void
    {
        if (blank($user->email)) {
            return;
        }

        try {
            Mail::raw($body, fn ($message) => $message->to($user->email)->subject($subject));
            EmailLog::query()->create([
                'receiver_email' => $user->email,
                'subject' => $subject,
                'body_summary' => $body,
                'sent_at' => now(),
                'status' => 'sent',
            ]);
        } catch (Throwable $exception) {
            EmailLog::query()->create([
                'receiver_email' => $user->email,
                'subject' => $subject,
                'body_summary' => $body,
                'sent_at' => now(),
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
