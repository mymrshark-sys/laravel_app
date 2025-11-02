<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $query = Auth::user()->tasks()->latest();
        
        if ($filter === 'completed') {
            $query->completed();
        } elseif ($filter === 'pending') {
            $query->pending();
        }
        
        $tasks = $query->paginate(10);
        
        return view('tasks.index', compact('tasks', 'filter'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request)
    {
        Auth::user()->tasks()->create($request->validated());
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil ditambahkan!');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $task->update($request->validated());
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diupdate!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->toggleComplete();
        
        return back()->with('success', 'Status task berhasil diubah!');
    }
}