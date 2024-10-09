<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;


class ProjectController extends Controller
{

    // List all projects (Employee)
    public function index()
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
#     
    // Delete a project (HOD only)
    public function destroy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $project->delete();

        return response()->json(null, 204);
    }

    // Employee updates project status (Employee only)
    public function updateStatus(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $request->validate([
            'status' => 'required|string|in:Active,Completed,On Hold,Cancelled,Pending',
        ]);

        $project->status = $request->status;
        $project->save();

        return response()->json($project, 200);
    }
}
