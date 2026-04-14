<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Support\AccessMatrix;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'start_date' => $project->start_date,
                'description' => $project->description,
            ])
            ->values();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'scope' => 'all',
            'title' => 'Danh sach du an',
        ]);
    }

    public function myProjects(Request $request): Response
    {
        $user = $request->user();
        $profileId = $user->employeeProfile?->id;

        if ($user->hasAnyRole([AccessMatrix::ROLE_ADMIN, AccessMatrix::ROLE_HR])) {
            return $this->index($request);
        }

        $projectIds = ProjectMember::query()
            ->where('employee_profile_id', $profileId)
            ->where('is_active', true)
            ->pluck('project_id');

        $projects = Project::query()
            ->whereIn('id', $projectIds)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'start_date' => $project->start_date,
                'description' => $project->description,
            ])
            ->values();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'scope' => 'mine',
            'title' => 'Du an cua toi',
        ]);
    }
}
