<?php

namespace App\Http\Controllers\Api;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;

use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Optionally apply filters and sorting
    
            $permissions = Permission::query();
    
            // Apply filters if provided in the request
    
            if ($request->has('filter')) {
                $permissions = $permissions->where($request->filter);
            }
    
            if ($request->has('sort')) {
                $permissions = $permissions->orderBy($request->sort);
            }
    
            $permissions = $permissions->paginate(20); // Adjust page size as needed
    
            return response()->json([
                'success' => true,
                'data' => PermissionResource::Collection($permissions),
            ]);
        } catch (Exception $e) {
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
    public function store(StorePermissionRequest $request)
    {
        // Informative message before validation
        if (!$request->has('name') || empty($request->input('name'))) {
            return response()->json(['message' => 'Permission name is required.'], 422);
        }
    
        $validated = $request->validated(); // Get validated data
    
        try {
            $permission = Permission::create($validated);
    
            // ... (further actions with the permission successful creation)
    
            return response()->json($permission, 201); // Created response
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
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404); // Status code 404 for "Not Found"
        }
        return response()->json($permission, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
      */
    // public function update(UpdatePermissionRequest $request, $id)
    // {
    //     $validated = $request->validated(); // Get validated data
    //     $permission = Permission::find($id);
    
    //     if (!$permission) {
    //         return response()->json(['message' => 'Permission not found'], 404); // Status code 404 for "Not Found"
    //     }
    
    //     if (isset($validated['name']) && $validated['name'] !== '') {
    //         if ($permission->name !== $validated['name']) {
    //             $unique = Permission::where('name', $validated['name'])->first();
    
    //             if ($unique) {
    //                 return response()->json(['message' => 'This name is already taken.'], 422); // Status code 422 for "Unprocessable Entity"
    //             }
    //         }
    
    //         $permission->name = $validated['name'];
    //     }
    
    //     $permission->save();
    
    //     return response()->json($permission);
    // }
    public function update(UpdatePermissionRequest $request, $id)
{
    $validated = $request->validated(); // Get validated data
    $permission = Permission::find($id);

    if (!$permission) {
        return response()->json(['message' => 'Permission not found'], 404); // Status code 404 for "Not Found"
    }

    if (isset($validated['name']) && $validated['name'] !== '') {
        $permission->name = $validated['name'];
    }

    $permission->save();

    return response()->json($permission);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rowCount = DB::table('permissions')
            ->where('id', $id)
            ->delete();
        if ($rowCount === 0) {
            return response()->json(['message' => 'Permission not found'], 404);
        }
    
        return response()->json(['message' => 'Permission deleted successfully'], 204);
        
    }
}
