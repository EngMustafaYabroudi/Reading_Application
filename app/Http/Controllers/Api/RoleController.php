<?php

namespace App\Http\Controllers\Api;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Role\StoreRoleRequest;
use Illuminate\Http\Client\RequestException;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['store']]);
        $this->middleware('permission:role-edit', ['only' => ['update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     // $roles = Role::orderBy('id', 'DESC')->paginate(5);

    //     // return response()->json([
    //     //   'data' => $roles->toArray()['data'],
    //     // ], 200); // Status code 200 for "OK"
    //     return RoleResource::collection(Role::all()); // Returns a collection of RoleResource objects
    // }

    public function index(Request $request)
    {
        try {
            // Optionally apply filters and sorting
    
            $roles = Role::query();
    
            // Apply filters if provided in the request
    
            if ($request->has('filter')) {
                $roles = $roles->where($request->filter);
            }
    
            if ($request->has('sort')) {
                $roles = $roles->orderBy($request->sort);
            }
    
            $roles = $roles->paginate(10); // Adjust page size as needed
    
            return response()->json([
                'success' => true,
                'data' => RoleResource::Collection($roles),
            ]);
        } catch (RequestException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated(); // Get validated data
        // Informative message before validation
        if (!$request->has('name') || empty($request->input('name'))) {
            return response()->json(['message' => 'Permission name is required.'], 422);
        }
        try {
            $role = Role::create($validated);
            if ($request->has('permissions') && is_array($request->input('permissions'))) {
                $role->syncPermissions($request->input('permissions'));
            }
            return response()->json([
                'success' => true,
                'data' => new RoleResource($role),
            ]);
        } catch (Exception $e) {
            // Handle potential database errors gracefully
            return response()->json(['message' => 'Permission creation failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404); // Status code 404 for "Not Found"
        }

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id)
{
    try {
        // Perform data validation as usual
        $validated = $request->validated();
        // Attempt to find the role
        $role = Role::find($id);
        if (!$role) {
            // Handle role not found exception
            throw new ModelNotFoundException('Role with ID ' . $id . ' not found');
        }

        // Update role attributes
        if (isset($validated['name']) && $validated['name'] !== '') {
            $role->name = $validated['name'];
        }

        // Get existing permissions (Collection)
        $existingPermissions = $role->permissions->pluck('id');

        // Convert Collection to array
        $existingPermissionsArray = $existingPermissions->toArray();

        // Get new permissions from the request
        $newPermissions = $request->input('permissions');

        // Merge new permissions with existing permissions
        $mergedPermissions = array_merge($existingPermissionsArray, $newPermissions);

        // Sync permissions with the merged list
        $role->syncPermissions($mergedPermissions);

        // Save the role, catching potential database exceptions
        $role->save();

        // Return success response with the updated role
        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
        ]);
    } catch (ModelNotFoundException $e) {
        // Handle role not found error gracefully
        return response()->json(['message' => 'Role not found'], 404);
    } catch (Exception $e) {
        // Catch any other unexpected exceptions
        report($e);  // Optionally report the exception for logging or debugging
        return response()->json(['message' => 'An error occurred while updating the role'], 500);
    } finally {
        // Optional cleanup code that always executes, regardless of exceptions
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
    
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
    
        $role->delete();
    
        return response()->json(['message' => 'Role deleted successfully'], 204);
    }
    
    

}
