<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the primary key as 'task_id'
    protected $primaryKey = 'task_id';

    // If your primary key is auto-incrementing
    public $incrementing = true;

    // Specify the data type for your primary key (integer)
    protected $keyType = 'int';

    // Define the fillable fields
    protected $fillable = [
        'task_name',
        'description',
        'start_date',
        'due_date',
        'status',
        'project_id',
        'employee_id',
    ];
    public function project()
    {
       return $this->belongsTo(Project::class, 'project_id'); 
    }
   public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id'); // assuming 'employee_id' is the foreign key
    }

}

