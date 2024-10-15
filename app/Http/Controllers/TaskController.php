<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    // Create a new task// Create a new task (Employee only)
/**
 * @OA\Post(
 *      path="/api/hods/tasks",
 *      security={{"Bearer": {}}},
 *      operationId="addTask",
 *      tags={"add task under hods middleware"},
 *      summary="adding new task in the system",
 *      description="Authenticated employee will add a new task to the system",
 *      
 *      @OA\Parameter(
 *          name="task_name",
 *          description="Name of the task",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="description",
 *          description="Task description",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="start_date",
 *          description="Task start date",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="due_date",
 *          description="Task due date",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="status",
 *          description="Task status (Pending, In Progress, Completed)",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="project_id",
 *          description="ID of the associated project",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="employee_id",
 *          description="ID of the authenticated employee",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      
 *      @OA\Response(
 *          response=201,
 *          description="Task successfully created",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="task_name", type="string", example="Task 1"),
 *              @OA\Property(property="description", type="string", example="Description of Task 1"),
 *              @OA\Property(property="start_date", type="string", format="date", example="2024-10-14"),
 *              @OA\Property(property="due_date", type="string", format="date", example="2024-10-20"),
 *              @OA\Property(property="status", type="string", example="Pending"),
 *              @OA\Property(property="project_id", type="integer", example=1),
 *              @OA\Property(property="employee_id", type="integer", example=2),
 *              @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-14T12:34:56Z"),
 *              @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-14T12:34:56Z")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad input"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden"
 *      )
 * )
 */


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
// Update an existing task (Employee only)
/**
 * @OA\Put(
 *      path="/api/hods/tasks/{task_id}",
 *      security={{"Bearer": {}}},
 *      operationId="updateTask",
 *      tags={"update task under Employee middleware"},
 *      summary="Update an existing task in the system",
 *      description="Authenticated employee will update an existing task in the system",
 *      
 *      @OA\Parameter(
 *          name="task_id",
 *          description="ID of the task to update",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="task_name",
 *          description="Name of the task",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="description",
 *          description="Task description",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="start_date",
 *          description="Task start date",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="due_date",
 *          description="Task due date",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="status",
 *          description="Task status (Pending, In Progress, Completed)",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="project_id",
 *          description="ID of the associated project",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="employee_id",
 *          description="ID of the authenticated employee",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      
 *      @OA\Response(
 *          response=200,
 *          description="Task successfully updated",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="task_name", type="string", example="Updated Task"),
 *              @OA\Property(property="description", type="string", example="Updated description of the task"),
 *              @OA\Property(property="start_date", type="string", format="date", example="2024-10-14"),
 *              @OA\Property(property="due_date", type="string", format="date", example="2024-10-20"),
 *              @OA\Property(property="status", type="string", example="In Progress"),
 *              @OA\Property(property="project_id", type="integer", example=1),
 *              @OA\Property(property="employee_id", type="integer", example=2),
 *              @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-14T12:34:56Z")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad input"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Task not found"
 *      )
 * )
 */

    // Update a task
    public function update(Request $request, $task_id)
{
    // Validate the request input
    $validator = Validator::make($request->all(), [
        'task_name' => 'required|string|max:255', 
        'description' => 'required|string',
        'start_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:start_date',  // Ensuring due_date is not before start_date
        'status' => 'required|in:Pending,In Progress,Completed',
        'project_id' => 'required|exists:projects,project_id',
        'employee_id' => 'required|exists:employees,id',
    ], [
        // Custom error messages
        'task_name.required' => 'The task name is required.',
        'description.required' => 'The task description is required.',
        'start_date.required' => 'The start date is required.',
        'due_date.required' => 'The due date is required.',
        'due_date.after_or_equal' => 'The due date must be a date after or equal to the start date.',
        'status.in' => 'The status must be one of the following: Pending, In Progress, Completed.',
        'project_id.exists' => 'The selected project does not exist.',
        'employee_id.exists' => 'The selected employee does not exist.',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422); // Unprocessable Entity
    }

    // Find the task by its ID
    $task = DB::table('tasks')->where('task_id', $task_id)->first();

    // Check if the task exists
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404); // Not Found
    }

    $updateData = [
        'task_name' => $request->task_name,
        'description' => $request->description,
        'start_date' => $request->start_date,
        'due_date' => $request->due_date,
        'status' => $request->status,
        'project_id' => $request->project_id,
        'employee_id' => $request->employee_id,
    ];

    // Update the task data
    DB::table('tasks')->where('task_id', $task_id)->update($updateData);

    // Log the update and return the response
    Log::info('Task updated successfully', $updateData);

    return response()->json(['message' => 'Task updated successfully', 'task' => $updateData], 200);
}
/**
     * @OA\Delete(
     *      path="/api/hods/tasks/{task_id}",
     *      security={{"Bearer": {}}},
     *      operationId="delete tasks",
     *      tags={"delete tasks under Hods middleware"},
     *      summary="delete new task in the system",
     *      description="authanticate hod will delete new tasks to the system ",
     *      @OA\Parameter(
     *          name="task_id",
     *          description="tasks id to be delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     
     *      @OA\Response(
     *          response=400,
     *          description="Bad hod input"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *      )
     * )
     */
    // Delete a task
    public function destroy($task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->delete();

        return response()->json(null, 204);
    }
    /**
     
     * @OA\Get(
     *     path="/api/hods/alltasks",
     *     security={{"Bearer": {}}},
     *     summary="fetch all tasks",
     *     @OA\Response(
     *         response="200",
     *         description="Successful response"
     *     )
     * )
     */
    // List all tasks
    public function index()
    {
        $tasks = Task::with(['project', 'employee'])->get();
        return response()->json($tasks, 200);
    }
}
