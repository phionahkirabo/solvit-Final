<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    // Create a new comment
    /**
 * @OA\Post(
 *      path="/api/tasks/{task_id}/comments",
 *      security={{"Bearer": {}}},
 *      operationId="addComment",
 *      tags={" Task comments"},
 *      summary="Add a new comment on an existing task",
 *      description="Authenticated HODs and employees can add a new comment to the task.",
 *    
 *      
 *      @OA\Parameter(
 *          name="comment",
 *          description="Comment from HOD or employee",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="task_id",
 *          description="tasks tht it is commented",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="hod_id",
 *          description="auth hod that added  the comment",
 *          required=false,
 *          in="query",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="employee_id",
 *          description="employee ID  who the comment is related",
 *          required=false,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      
 *      @OA\Response(
 *          response=201,
 *          description="Comment successfully created",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="comment", type="string", example="This is a new comment for the task."),
 *              @OA\Property(property="task_id", type="integer", example=10),
 *              @OA\Property(property="hod_id", type="integer", example="HOD  id who is authnticated"),
 *              @OA\Property(property="employee_id", type="integer", example="employee id who is authnticated"),
 *              
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

   
    

    // Store a new comment
    public function store(Request $request, $task_id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        // Determine if the commenter is a HOD or an Employee
        $hod_id = auth()->guard('hod')->check() ? auth()->guard('hod')->id() : null;
        $employee_id = auth()->guard('employee')->check() ? auth()->guard('employee')->id() : null;

        // Ensure that one of the IDs is always null if the other is present
        if ($hod_id && $employee_id) {
            return response()->json(['error' => 'Both HOD and Employee cannot be authenticated at the same time'], 400);
        }

        // Create a new task comment
        $comment = TaskComment::create([
            'comment' => $request->input('comment'),
            'task_id' => $task_id,
            'hod_id' => $hod_id,                // Set hod_id if HOD is authenticated
            'employee_id' => $employee_id,      // Set employee_id if Employee is authenticated
        ]);

        return response()->json([
            'message' => 'Comment successfully added',
            'comment' => $comment
        ], 201);
    }
     // Get all comments for a specific task
    /**
     * @OA\Get(
     *      path="/api/tasks/{task_id}/comments/index",
     *      security={{"Bearer": {}}},
     *      operationId="getComments",
     *      tags={"Task comments"},
     *      summary="Get all comments for a specific task",
     *      description="Retrieve all comments associated with a task.",
     *      @OA\Parameter(
     *          name="task_id",
     *          description="ID of the task",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="List of comments",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(property="task_id", type="integer"),
     *                  @OA\Property(property="hod_id", type="integer"),
     *                  @OA\Property(property="employee_id", type="integer"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Task not found"
     *      )
     * )
     */
    public function index($task_id)
    {
        $comments = TaskComment::where('task_id', $task_id)->get();
        return response()->json($comments);
    }

    // Show a specific comment
    /**
     * @OA\Get(
     *      path="/api/comments/{comment_id}",
     *      security={{"Bearer": {}}},
     *      operationId="showComment",
     *      tags={"Task comments"},
     *      summary="Show a specific comment",
     *      description="Retrieve a specific comment by ID.",
     *      @OA\Parameter(
     *          name="comment_id",
     *          description="ID of the comment",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Comment retrieved successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              
     *              @OA\Property(property="comment", type="string"),
     *              @OA\Property(property="task_id", type="integer"),
     *              @OA\Property(property="hod_id", type="integer"),
     *              @OA\Property(property="employee_id", type="integer"),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found"
     *      )
     * )
     */
    public function show($comment_id)
    {
        $comment = TaskComment::find($comment_id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        return response()->json($comment);
    }

    // Update a comment
    /**  
     * @OA\Put(
     *      path="/api/comments/{comment_id}",
     *      security={{"Bearer": {}}},
     *      operationId="updateComment",
     *      tags={"Task comments"},
     *      summary="Update a specific comment",
     *      description="Authenticated HODs and employees can update a comment.",
     *      @OA\Parameter(
     *          name="comment_id",
     *          description="ID of the comment to update",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="comment",
     *          description="Updated comment text",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Comment successfully updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Comment successfully updated"),
     *              @OA\Property(property="comment", type="object", 
     *                  
     *                  @OA\Property(property="comment", type="string", example="Updated comment text."),
     *                  @OA\Property(property="task_id", type="integer", example=10),
     *                  @OA\Property(property="hod_id", type="integer", example="HOD ID who is authenticated"),
     *                  @OA\Property(property="employee_id", type="integer", example="Employee ID who is authenticated"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found"
     *      )
     * )
     */
    public function update(Request $request, $comment_id)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = TaskComment::find($comment_id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->comment = $validated['comment'];
        $comment->save();

        return response()->json([
            'message' => 'Comment successfully updated',
            'comment' => $comment
        ]);
    }

    // Delete a comment
    /**
     * @OA\Delete(
     *      path="/api/comments/{comment_id}",
     *      security={{"Bearer": {}}},
     *      operationId="deleteComment",
     *      tags={"Task comments"},
     *      summary="Delete a specific comment",
     *      description="Authenticated HODs and employees can delete a comment.",
     *      @OA\Parameter(
     *          name="comment_id",
     *          description="ID of the comment to delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Comment successfully deleted",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Comment successfully deleted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found"
     *      )
     * )
     */
    public function destroy($comment_id)
    {
        $comment = TaskComment::find($comment_id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment successfully deleted']);
    }

}