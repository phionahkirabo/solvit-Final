<?php
namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // 1. List All Teams
    /**
     * @OA\Get(
     *      path="/api/hods/teamsindex",
     *      security={{"Bearer": {}}},
     *      operationId="getTeams",
     *      tags={"Teams"},
     *      summary="Get all teams",
     *      description="Authenticated HODs can view all teams with employee and HOD details.",
     *    
     *      @OA\Response(
     *          response=200,
     *          description="List of teams",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="team_id", type="integer", example=1),
     *                  @OA\Property(property="full_name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_number", type="string", example="ID12345"),
     *                  @OA\Property(property="nationality", type="string", example="American"),
     *                  @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                  @OA\Property(property="team", type="string", example="AI"),  // Add the 'team' property
     *                  @OA\Property(property="gender", type="string", example="Male"),
     *                  @OA\Property(property="profile_picture", type="string", example="https://example.com/profile.jpg"),
     *                  @OA\Property(
     *                      property="hod",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Jane Smith")
     *                  ),
     *                  @OA\Property(
     *                      property="employee",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=2),
     *                      @OA\Property(property="name", type="string", example="John Doe")
     *                  )
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
    public function index()
    {
        
        $teams = Team::with(['hod', 'employee'])->get();
        return response()->json(['teams' => $teams], 200);
    }

    // 2. Show a Single Team
    /**
     * @OA\Get(
     *      path="/api/hods/teams/{team_id}",
     *      security={{"Bearer": {}}},
     *      operationId="showTeam",
     *      tags={"Teams"},
     *      summary="Get a specific team by ID",
     *      description="Authenticated HODs can retrieve a team by its ID, along with associated HOD and employee details.",
     *    
     *      @OA\Parameter(
     *          name="team_id",
     *          description="ID of the team to retrieve",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="Team details",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="team_id", type="integer", example=1),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="id_number", type="string", example="ID12345"),
     *              @OA\Property(property="nationality", type="string", example="American"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              @OA\Property(property="team", type="string", example="AI"), // Add 'team' field here
     *              @OA\Property(property="gender", type="string", example="Male"),
     *              @OA\Property(property="profile_picture", type="string", example="https://example.com/profile.jpg"),
     *              @OA\Property(
     *                  property="hod",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Jane Smith")
     *              ),
     *              @OA\Property(
     *                  property="employee",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=2),
     *                  @OA\Property(property="name", type="string", example="John Doe")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Team not found"
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
    public function show($team_id)
    {
        $team = Team::with(['hod', 'employee'])->find($team_id);
        if ($team) {
            return response()->json(['team' => $team], 200);
        }
        return response()->json(['message' => 'Team not found'], 404);
    }

    // 3. Create a New Team
    /**
     * @OA\Post(
     *      path="/api/hods/teams",
     *      security={{"Bearer": {}}},
     *      operationId="createTeam",
     *      tags={"Teams"},
     *      summary="Create a new team entry",
     *      description="Authenticated HODs can create a new team entry with associated HOD and employee data.",
     *    
     *      @OA\Parameter(
     *          name="hod_id",
     *          description="ID of the HOD associated with the team",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="employee_id",
     *          description="ID of the employee associated with the team",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="full_name",
     *          description="Full name of the team member",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id_number",
     *          description="Unique identification number for the team member",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="nationality",
     *          description="Nationality of the team member",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Email of the team member",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="gender",
     *          description="Gender of the team member",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"Male", "Female", "Other"}
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="profile_picture",
     *          description="Profile picture of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="binary"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=201,
     *          description="Team created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="team", type="object",
     *                  @OA\Property(property="team_id", type="integer", example=1),
     *                  @OA\Property(property="hod_id", type="integer", example=1),
     *                  @OA\Property(property="employee_id", type="integer", example=2),
     *                  @OA\Property(property="full_name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_number", type="string", example="ID12345"),
     *                  @OA\Property(property="nationality", type="string", example="American"),
     *                  @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                  @OA\Property(property="gender", type="string", example="Male"),
     *                  @OA\Property(property="team", type="string", example="AI") // Added the 'team' field
     *                  @OA\Property(property="profile_picture", type="string", example="profile_pictures/default.jpg")
     *              ),
     *              @OA\Property(property="message", type="string", example="Team created successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error or bad input"
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
    public function store(Request $request)
    {
        $request->validate([
            'hod_id' => 'required|exists:hods,id',
            'employee_id' => 'required|exists:employees,id',
            'profile_picture' => 'nullable|image|max:2048',
            'full_name' => 'required|string',
            'id_number' => 'required|string|unique:teams,id_number|digits:16',
            'nationality' => 'required|string',
            'email' => 'required|email|unique:teams,email',
            'gender' => 'required|in:Male,Female,Other',
            'team' => 'required|in:Data Science and Information Systems,Cybersecurity,Project Management,Research,MEAL,Marketing,Sales and Revenue,AI', 
        ]);

        $data = $request->all();
        
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $team = Team::create($data);

        return response()->json(['team' => $team, 'message' => 'Team created successfully'], 201);
    }

    // 4. Update a Team
    /**
     * @OA\Put(
     *      path="/api/hods/teamsUpdate/{team_id}",
     *      security={{"Bearer": {}}},
     *      operationId="update",
     *      tags={"Teams"},
     *      summary="Update an existing team entry",
     *      description="Authenticated HODs can update details of an existing team entry.",
     *    
     *      @OA\Parameter(
     *          name="team_id",
     *          description="ID of the team member to update",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="hod_id",
     *          description="ID of the HOD associated with the team",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="employee_id",
     *          description="ID of the employee associated with the team",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="full_name",
     *          description="Full name of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="id_number",
     *          description="Unique identification number for the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="nationality",
     *          description="Nationality of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Email of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="gender",
     *          description="Gender of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"Male", "Female", "Other"}
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="profile_picture",
     *          description="Profile picture of the team member",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="binary"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="Team updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="team", type="object",
     *                  @OA\Property(property="team_id", type="integer", example=1),
     *                  @OA\Property(property="hod_id", type="integer", example=1),
     *                  @OA\Property(property="employee_id", type="integer", example=2),
     *                  @OA\Property(property="full_name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_number", type="string", example="ID12345"),
     *                  @OA\Property(property="nationality", type="string", example="American"),
     *                  @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                  @OA\Property(property="gender", type="string", example="Male"),
     *                  @OA\Property(property="team", type="string", example="AI") // Added 'team' field
     *                  @OA\Property(property="profile_picture", type="string", example="profile_pictures/default.jpg")
     *              ),
     *              @OA\Property(property="message", type="string", example="Team updated successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error or bad input"
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
     *          description="Team not found"
     *      )
     * )
     */


    public function update(Request $request, $team_id)
    {
        $team = Team::find($team_id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

       $request->validate([
            'profile_picture' => 'nullable|image|max:2048',
            'full_name' => 'required|string',
            'id_number' => 'required|string|digits:16|unique:teams,id_number,' . $team_id . ',team_id',
            'nationality' => 'required|string',
            'email' => 'required|email|unique:teams,email,' . $team_id . ',team_id',
            'gender' => 'required|in:Male,Female,Other',
            'team' => 'required|in:Data Science and Information Systems,Cybersecurity,Project Management,Research,MEAL,Marketing,Sales and Revenue,AI', 
        ]);

        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $team->update($data);

        return response()->json(['team' => $team, 'message' => 'Team updated successfully'], 200);
    }

    // 5. Delete a Team

    /**
     * @OA\Delete(
     *      path="/api/hods/teams/{team_id}",
     *      security={{"Bearer": {}}},
     *      operationId="deleteTeam",
     *      tags={"Teams"},
     *      summary="Delete a team member",
     *      description="Authenticated HODs can delete a team member by ID.",
     *    
     *      @OA\Parameter(
     *          name="team_id",
     *          description="ID of the team member to delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="Team deleted successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Team deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Team not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Team not found")
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
    public function destroy($team_id)
    {
        $team = Team::find($team_id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        $team->delete();

        return response()->json(['message' => 'Team deleted successfully'], 200);
    }
}

