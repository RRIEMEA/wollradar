<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        if ($request->boolean('quick_add')) {
            return $this->storeQuickAdd($request);
        }

        $data = $this->validateProject($request);

        Project::query()->create([
            'user_id' => auth()->id(),
            'name'    => trim($data['name']),
            'notes'   => $data['notes'] ?? null,
            'is_finished' => (bool) ($data['is_finished'] ?? false),
        ]);

        return redirect()->route('projects.index')->with('status', 'Projekt gespeichert.');
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
            'is_finished' => (bool) ($data['is_finished'] ?? false),
        ]);

        return redirect()->route('projects.edit', $project)->with('status', 'Projekt aktualisiert.');
    }

    public function destroy(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Projekt gelöscht.');
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
            'is_finished' => ['nullable', 'boolean'],
        ]);
    }

    private function storeQuickAdd(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'quick_project_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('projects', 'name')
                    ->where('user_id', $userId),
            ],
            'quick_project_notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->quickCreateValidationRedirect(
                $request,
                'projects.index',
                $validator,
                'quickAddProject',
                'quick-add-project'
            );
        }

        $project = Project::query()->create([
            'user_id' => $userId,
            'name' => trim((string) $request->input('quick_project_name')),
            'notes' => $request->filled('quick_project_notes') ? (string) $request->input('quick_project_notes') : null,
        ]);

        return $this->quickCreateSuccessRedirect(
            $request,
            'projects.index',
            'Projekt angelegt.',
            'project_id',
            $project->id
        );
    }
}
