<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);

        Project::query()->create([
            'user_id' => auth()->id(),
            'name'    => trim($data['name']),
            'notes'   => $data['notes'] ?? null,
        ]);

        return redirect()->route('projects.index')->with('status', 'Project saved.');
    }

    public function edit(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $data = $this->validateProject($request, $project->id);

        $project->update([
            'name'  => trim($data['name']),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('projects.edit', $project)->with('status', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Project deleted.');
    }

    private function validateProject(Request $request, ?int $ignoreId = null): array
    {
        $userId = auth()->id();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('projects', 'name')
                    ->where('user_id', $userId)
                    ->ignore($ignoreId),
            ],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
