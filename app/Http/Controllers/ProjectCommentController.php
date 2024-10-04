<?php

namespace App\Http\Controllers;

use App\Models\ProjectComment;
use Illuminate\Http\Request;

class ProjectCommentController extends Controller
{
    // List all comments for a project (Employee)
    public function index($project_id)
    {
        $comments = ProjectComment::where('project_id', $project_id)->get();
        return response()->json($comments, 200);
    }

    // Employee adds a comment to the project (Employee)
    public function store(Request $request, $project_id)
    {
        $request->validate([
            'comment' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'status_update' => 'nullable|string|in:Active,Completed,On Hold,Cancelled,Pending',
        ]);

        $comment = new ProjectComment();
        $comment->project_id = $project_id;
        $comment->employee_id = $request->employee_id;
        $comment->comment = $request->comment;
        $comment->status_update = $request->status_update ?? 'Active';
        $comment->save();

        return response()->json($comment, 201);
    }
}
