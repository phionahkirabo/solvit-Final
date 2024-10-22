<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $table = 'task_comments';

    protected $fillable = [
        'comment',
        'task_id',
        'hod_id',         // Add a field for HOD relationship
        'employee_id',    // Add a field for Employee relationship
    ];

   
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_ id', 'id'); // Ass    uming 'id' is the primary key in the Task table
    }


    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id', 'id'); // Assuming 'id' is the primary key in the Hod table
    }

   
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id'); // Assuming 'id' is the primary key in the Employee table
    }
}
