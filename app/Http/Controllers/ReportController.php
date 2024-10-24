<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{

        /**
     * @OA\Get(
     *      path="/api/reportsindex",
     *      security={{"Bearer": {}}},
     *      operationId="getReports",
     *      tags={"Reports"},
     *      summary="Get all reports",
     *      description="Authenticated HODs and employees can view all the reports.",
     *    
     *      @OA\Response(
     *          response=200,
     *          description="List of reports",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="title", type="string", example="Quarterly Review"),
     *                  @OA\Property(property="content", type="string", example="This is the report content..."),
     *                  @OA\Property(property="task_id", type="integer", example=1),
     *                  @OA\Property(property="employee_id", type="integer", example=2),
     *                  @OA\Property(property="hod_id", type="integer", example=1),
     *              )
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

   // Get all reports
    public function index()
    {
        $reports = Report::all();
        return response()->json($reports);
    }
    
        /**
     * @OA\Post(
     *      path="/api/reports",
     *      security={{"Bearer": {}}},
     *      operationId="createReport",
     *      tags={"Reports"},
     *      summary="Create a new report",
     *      description="Authenticated HODs or employees can create a new report for a specific task.",
     *    
     *      @OA\Parameter(
     *          name="title",
     *          description="Title of the report",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="content",
     *          description="Content of the report",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="task_id",
     *          description="Task ID related to the report",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="employee_id",
     *          description="Employee ID (optional) who submitted the report",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="hod_id",
     *          description="HOD ID (optional) who submitted the report",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=201,
     *          description="Report successfully created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Quarterly Review"),
     *              @OA\Property(property="content", type="string", example="This report contains the quarterly review of the project..."),
     *              @OA\Property(property="task_id", type="integer", example=1),
     *              @OA\Property(property="employee_id", type="integer", example=2),
     *              @OA\Property(property="hod_id", type="integer", example=1),
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

    // Create a new report
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'task_id' => 'required|exists:tasks,task_id', // Ensure task_id exists
            'employee_id' => 'nullable|exists:employees,id',
            'hod_id' => 'nullable|exists:hods,id',
        ]);

        $report = Report::create([
            'title' => $request->title,
            'content' => $request->content,
            'task_id' => $request->task_id,
            'employee_id' => $request->employee_id,
            'hod_id' => $request->hod_id,
        ]);

        return response()->json($report, 201);
    }


        /**
     * @OA\Get(
     *      path="/api/reports/{id}",
     *      security={{"Bearer": {}}},
     *      operationId="showReport",
     *      tags={"Reports"},
     *      summary="Get a specific report by ID",
     *      description="Authenticated HODs or employees can retrieve a report by its ID.",
     *    
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the report to retrieve",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="Report details",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Quarterly Review"),
     *              @OA\Property(property="content", type="string", example="This is the report content..."),
     *              @OA\Property(property="task_id", type="integer", example=1),
     *              @OA\Property(property="employee_id", type="integer", example=2),
     *              @OA\Property(property="hod_id", type="integer", example=1),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Report not found"
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

    // Get a specific report
    public function show($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report);
    }


        /**
     * @OA\Put(
     *      path="/api/reports/{report_id}",
     *      security={{"Bearer": {}}},
     *      operationId="updateReport",
     *      tags={"Reports"},
     *      summary="Update an existing report",
     *      description="Authenticated HODs or employees can update an existing report.",
     *    
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the report to update",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          description="Updated title of the report",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="content",
     *          description="Updated content of the report",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Report successfully updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="report_id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Updated Quarterly Review"),
     *              @OA\Property(property="content", type="string", example="This is the updated report content..."),
     *              @OA\Property(property="task_id", type="integer", example=1),
     *              @OA\Property(property="employee_id", type="integer", example=2),
     *              @OA\Property(property="hod_id", type="integer", example=1),
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

    // Update a specific report
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'task_id' => 'sometimes|required|exists:tasks,id',
            'employee_id' => 'nullable|exists:employees,id',
            'hod_id' => 'nullable|exists:hods,id',
        ]);

        $report->update($request->only(['title', 'content', 'task_id', 'employee_id', 'hod_id']));

        return response()->json($report);
    }

        /**
     * @OA\Delete(
     *      path="/api/reports/{report_id}",
     *      security={{"Bearer": {}}},
     *      operationId="deleteReport",
     *      tags={"Reports"},
     *      summary="Delete an existing report",
     *      description="Only authenticated HODs can delete a report.",
     *    
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the report to delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Report successfully deleted"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Report not found"
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

    // Delete a specific report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json(null, 204);
    }             
}
