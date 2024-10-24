<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;
    protected $primaryKey = 'comment_id';
    // The table associated with the model (optional if table follows Laravel convention)
    protected $table = 'task_comments';

    // The attributes that are mass assignable
    
    protected $fillable = ['comment', 'task_id', 'hod_id', 'employee_id'];

    // TaskComment belongs to a Task
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // TaskComment belongs to a HOD (if applicable)
    public function hod(): BelongsTo
    {
        return $this->belongsTo(Hod::class);
    }

    // TaskComment belongs to an Employee (if applicable)
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}