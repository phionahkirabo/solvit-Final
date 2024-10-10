<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Create a new task
    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'project_id' => 'required|exists:projects,project_id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'project_id' => $request->project_id,
            'employee_id' => $request->employee_id,
        ]);

        return response()->json($task, 201);
    }

    // Show a task
    public function show($task_id)
    {
        $task = Task::with(['project', 'employee'])->findOrFail($task_id);
        return response()->json($task, 200);
    }

    // Update a task
    public function update(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'project_id' => 'required|exists:projects,project_id',
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        $task->update([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'project_id' => $request->project_id,
            'employee_id' => $request->employee_id,
        ]);

        return response()->json($task, 200);
    }

    // Delete a task
    public function destroy($task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->delete();

        return response()->json(null, 204);
    }

    // List all tasks
    public function index()
    {
        $tasks = Task::with(['project', 'employee'])->get();
        return response()->json($tasks, 200);
    }
}
