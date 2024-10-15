<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Log;


class ProjectController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/employees/projects",
     *     security={{"Bearer": {}}},
     *     summary="fetch all tasks",
     *     @OA\Response(
     *         response="200",
     *         description="Successful response"
     *     )
     * )
     */
    // List all projects (Employee)
    public function employeesindex()
    {
        $projects = Project::all();
        return response()->json($projects, 200);
    }

    // Show a specific project (Employee)
    public function show($project_id)
    {
        $project = Project::findOrFail($project_id);
        return response()->json($project, 200);
    }

    // Create a new project (HOD only)
 /**
     * @OA\Post(
     *      path="/api/hods/projects",
     *      security={{"Bearer": {}}},
     *      operationId="add project",
     *      tags={"add project underHods middleware"},
     *      summary="adding new project in the system",
     *      description="authanticate hod will add new project to the system ",
     *    
     *      @OA\Parameter(
     *          name="project_name",
     *          description="project name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="description",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     
     *     @OA\Parameter(
     *          name="start_date",
     *          description="start date",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="end_date",
     *          description="end_date",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="project_category",
     *          description="project category by the number P1,P2,P3",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="status",
     *          description="to see if the project it is Pending,On hold,Active,Cancelled,Completed",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="hod_id",
     *          description="Authanticated hod",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="hod successfully registered",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="project_name", type="string", example="emplyee's project management"),
     *              @OA\Property(property="description", type="string", example="this project it is about to fix  project managment time"),
     *              @OA\Property(property="start_date", type="string", example="2024/10/19"),
     *              @OA\Property(property="end_date", type="string", example="2024/10/29"),
     *              @OA\Property(property="project_category", type="string", example="like P1,p2,p3"),
     *              @OA\Property(property="status", type="string", example="Pending,On hold,Active,Cancelled,Completed"),
     *              @OA\Property(property="hod_id", type="string", example="1/2 or any other hod id"),
     *              
     *          )
     *      ),
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

public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'project_name' => 'required|string|max:255',  
        'description' => 'required|string',
        'start_date' => 'required|date_format:d/m/Y',  // Validate date with d/m/Y format
        'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',  // Ensure end_date is not before start_date
        'project_category' => 'required|string|max:255',
        'status' => 'required|string|in:Active,Completed,On Hold,Cancelled,Pending',
        'hod_id' => 'required|exists:hods,id',
    ], [
        // Custom error messages
        'project_name.required' => 'The project name is required.',
        'description.required' => 'The project description is required.',
        'start_date.required' => 'The start date is required.',
        'start_date.date_format' => 'The start date must be in the format dd/mm/yyyy.',
        'end_date.required' => 'The end date is required.',
        'end_date.date_format' => 'The end date must be in the format dd/mm/yyyy.',
        'end_date.after_or_equal' => 'The end date must be a date after or equal to the start date.',
        'project_category.required' => 'The project category is required.',
        'status.required' => 'The project status is required.',
        'status.in' => 'The status must be one of the following: Active, Completed, On Hold, Cancelled, Pending.',
        'hod_id.required' => 'The HOD ID is required.',
        'hod_id.exists' => 'The selected HOD does not exist.',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422); // Unprocessable Entity
    }

    // Convert the dates to Y-m-d format for MySQL
    try {
        $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
    } catch (\Exception $e) {
        // Log the error and return a proper response
        \Log::error('Date parsing error: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid date format.'], 400);
    }

    // Create the project with the formatted dates
    $project = Project::create([
        'project_name' => $request->project_name,
        'description' => $request->description,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'project_category' => $request->project_category,
        'status' => $request->status,
        'hod_id' => $request->hod_id,
    ]);

    return response()->json($project, 201);
}

    // Update a project (HOD only)
    
 /**
     * @OA\Post(
     *      path="/api/hods/projectsUpdate/{project_id}",
     *      security={{"Bearer": {}}},
     *      operationId="Update project",
     *      tags={"Update project under Hods middleware"},
     *      summary="Update new project in the system",
     *      description="authanticate hod will add new project to the system ",
     *      @OA\Parameter(
     *          name="project_id",
     *          description="project id to be updated",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="project_name",
     *          description="project name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="description",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     
     *     @OA\Parameter(
     *          name="start_date",
     *          description="start date",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="end_date",
     *          description="end_date",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="project_category",
     *          description="project category by the number P1,P2,P3",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="status",
     *          description="to see if the project it is Pending,On hold,Active,Cancelled,Completed",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="hod_id",
     *          description="Authanticated hod",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="hod successfully registered",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example="emplyee's project management"),
     *              @OA\Property(property="project_name", type="string", example="emplyee's project management"),
     *              @OA\Property(property="description", type="string", example="this project it is about to fix  project managment time"),
     *              @OA\Property(property="start_date", type="string", example="2024/10/19"),
     *              @OA\Property(property="end_date", type="string", example="2024/10/29"),
     *              @OA\Property(property="project_category", type="string", example="like P1,p2,p3"),
     *              @OA\Property(property="status", type="string", example="Pending,On hold,Active,Cancelled,Completed"),
     *              @OA\Property(property="hod_id", type="integer", example="1/2 or any other hod id"),
     *              
     *          )
     *      ),
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
public function update(Request $request, $project_id)
{
 $validator = Validator::make($request->all(), [
                'project_name' => 'required|string|max:255',  
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',  // Ensuring end_date is not before start_date
                'project_category' => 'required|string|max:255',
                'status' => 'required|string|in:Active,Completed,On Hold,Cancelled,Pending',
            ], [
                // Custom error messages
                'project_name.required' => 'The project name is required.',
                'description.required' => 'The project description is required.',
                'start_date.required' => 'The start date is required.',
                'end_date.required' => 'The end date is required.',
                'end_date.after_or_equal' => 'The end date must be a date after or equal to the start date.',
                'project_category.required' => 'The project category is required.',
                'status.in' => 'The status must be one of the following: Active, Completed, On Hold, Cancelled, Pending.',
                'status.required' => 'The project status is required.',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422); // Unprocessable Entity
            }

            // Find the project by its ID
            $project = DB::table('projects')->where('project_id', $project_id)->first();
            
            // Check if the project exists
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404); // Not Found
            }

    $updateData = [
        "project_name"=>$request->project_name,
        "description"=>$request->description,
        "start_date"=>$request->start_date,
        "project_category"=>$request->project_category,
        "end_date"=>$request->end_date,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
        "status"=>$request->status
    ];

    // Update the project data
    DB::table('projects')->where('project_id', $project_id)->update($updateData);

    // Log the update and return the response
    Log::info('Project updated successfully', $updateData);

    return response()->json(['message' => 'Project updated successfully', 'project' => $updateData], 200);
}
 /**
     * @OA\Delete(
     *      path="/api/hods/projectsDelete/{project_id}",
     *      security={{"Bearer": {}}},
     *      operationId="delete project",
     *      tags={"delete project under Hods middleware"},
     *      summary="delete new project in the system",
     *      description="authanticate hod will delete new project to the system ",
     *      @OA\Parameter(
     *          name="project_id",
     *          description="project id to be delete",
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
#     
    // Delete a project (HOD only)
    public function destroy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $project->delete();

        return response()->json(null, 204);
    }

 // Update project status (Employee only)
/**                                                                                                                                                                                                                                                                                                                                                                                                          
 * @OA\Put(
 *      path="/api/employees/projects/{project_id}/status",
 *      security={{"Bearer": {}}},
 *      operationId="updateProjectStatus",
 *      tags={"update project status under Employee middleware"},
 *      summary="Update the status of a project",
 *      description="Employee can update the status of the project (Pending, On Hold, Active, Cancelled, Completed)",
 *      
 *      @OA\Parameter(
 *          name="project_id",
 *          description="ID of the project to update",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="status",
 *          description="New status for the project",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string",
 *              enum={"Active", "Completed", "On Hold", "Cancelled", "Pending"}
 *          )
 *      ),
 *      
 *      @OA\Response(
 *          response=200,
 *          description="Project status updated successfully",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="project_id", type="integer", example=1),
 *              @OA\Property(property="status", type="string", example="Active")
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
 *          description="Project not found"
 *      )
 * )
 */

public function updateStatus(Request $request, $project_id)
    {
        // Find the project by its ID
        $project = Project::findOrFail($project_id);

        // Validate the incoming request
        $request->validate([
            'status' => 'required|string|in:Active,Completed,On Hold,Cancelled,Pending',
        ],[
            'status.required' => 'The project status is required.',
            'status.string' => 'The project status must be a string.',
            'status.in' => 'The status must be one of the following: Active, Completed, On Hold, Cancelled, Pending.',
        ]);

        // Update the project status
        $project->status = $request->status;
        $project->save();

        // Return the updated project details in the response
        return response()->json($project, 200);
    }
// Count projects by status (Employee only)
/**
 * @OA\Get(
 *      path="/api/employees/projects/status/count",
 *      security={{"Bearer": {}}},
 *      operationId="countProjectsByStatus",
 *      tags={"Count projects by status under Employee middleware"},
 *      summary="Get the count of projects by their status",
 *      description="Employee can retrieve the count of projects for each status (Active, Completed, On Hold, Cancelled, Pending).",
 *      
 *      @OA\Response(
 *          response=200,
 *          description="Successfully retrieved the project counts",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="Active", type="object", @OA\Property(property="count", type="integer", example=5)),
 *              @OA\Property(property="Completed", type="object", @OA\Property(property="count", type="integer", example=3)),
 *              @OA\Property(property="On Hold", type="object", @OA\Property(property="count", type="integer", example=2)),
 *              @OA\Property(property="Cancelled", type="object", @OA\Property(property="count", type="integer", example=1)),
 *              @OA\Property(property="Pending", type="object", @OA\Property(property="count", type="integer", example=4))
 *          )
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

public function countProjectsByStatus()
{
    // Define all possible statuses
    $statuses = ['Active', 'Completed', 'On Hold', 'Cancelled', 'Pending'];

    // Get the count of projects grouped by their status
    $projectCounts = DB::table('projects')
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->get()
        ->keyBy('status'); // Key the results by status for easy lookup

    // Prepare the final result with all statuses
    $result = [];
    foreach ($statuses as $status) {
        $result[$status] = [
            'count' => $projectCounts->get($status)->count ?? 0 // Default to 0 if no projects
        ];
    }

    return response()->json($result, 200);
}


}
