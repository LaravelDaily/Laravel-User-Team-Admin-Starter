<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct()
    {
        Gate::authorize('manage-tasks');
    }

    public function index(): View
    {
        $tasks = Task::paginate();

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(CreateTaskRequest $request): RedirectResponse
    {
        auth()->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Task created.');
    }

    public function edit(Task $task): View
    {
        abort_if($task->team_id !== auth()->user()->team_id, Response::HTTP_FORBIDDEN);

        return view('tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        abort_if($task->team_id !== auth()->user()->team_id, Response::HTTP_FORBIDDEN);

        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        abort_if($task->team_id !== auth()->user()->team_id, Response::HTTP_FORBIDDEN);

        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }
}
