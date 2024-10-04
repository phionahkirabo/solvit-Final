<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

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
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'project_category' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Completed,On Hold,Cancelled,Pending',
            'hod_id' => 'required|exists:hods,id',
        ]);

        $project = Project::create($request->all());

        return response()->json($project, 201);
    }

    // Update a project (HOD only)
    public function update(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $request->validate([
            'project_name' => 'string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'date',
            'end_date' => 'nullable|date',
            'project_category' => 'nullable|string|max:255',
            'status' => 'string|in:Active,Completed,On Hold,Cancelled,Pending',
        ]);

        $project->update($request->all());

        return response()->json($project, 200);
    }

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
