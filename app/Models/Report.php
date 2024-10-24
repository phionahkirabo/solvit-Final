<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'report_id';

    // If your primary key is auto-incrementing
    public $incrementing = true;

    // Specify the data type for your primary key (integer)
    protected $keyType = 'int';

    use HasFactory;

    protected $fillable = ['title', 'content', 'task_id', 'employee_id', 'hod_id'];

    // Report belongs to a Task
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Report belongs to an Employee (if applicable)
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Report belongs to a HOD (if applicable)
    public function hod(): BelongsTo
    {
        return $this->belongsTo(Hod::class);
    }
}
