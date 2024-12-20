    
    
// \Log::info('Update method called for project ID: ' . $project_id, $request->all());

//     // Find the project or fail if not found
//     $project = Project::findOrFail($project_id);

//     // Validate the request data
//     $request->validate([
//     'project_name' => 'required|string|max:255',  
//     'description' => 'nullable|string',
//     'start_date' => 'sometimes|date',
//     'end_date' => 'sometimes|nullable|date',
//     'project_category' => 'nullable|string|max:255',
//     'status' => 'sometimes|string|in:Active,Completed,On Hold,Cancelled,Pending',
// ]);
public function update(Request $request, $project_id)
{
 
    // Validate the request data
    
        $validator = Validator::make($request->all(), [
        'project_name' => 'required|string|max:255',  
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',  // Ensuring end_date is not before start_date
        'project_category' => 'required|string|max:255',
        'status' => 'required|string|in:Active,Completed,On Hold,Cancelled,Pending',
    ]);

    // Find the project by its ID
    
    
   
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
//////////////
#     <?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable implements JWTSubject
{
   use HasFactory, Notifiable,HasApiTokens;
   
     protected $fillable = [
        'employee_name',
        'email',
        'contact_number',
        'position',
        'hod_fk_id',
        'personalemail',
        'default_password',
        'password'
        
    ];
/**
* The attributes that should be hidden for arrays.
*
* @var array
*/
protected $hidden = [
 'password',
 'default_password'
];
/**
* Get the identifier that will be stored in the subject claim of the JWT.
*
* @return mixed
*/
public function getJWTIdentifier()
{
return $this->getKey();
}
/**
* Return a key value array, containing any custom claims to be added to the JWT.
*
* @return array
*/
public function getJWTCustomClaims()
{
return [];
}

  public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_fk_id');
  }
  public function tasks()
  {
      return $this->hasMany(Task::class, 'employee_id');
  }


}
