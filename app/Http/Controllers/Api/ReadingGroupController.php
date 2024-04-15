<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Traits\Sortable;
use App\Traits\Filterable;
use App\Traits\Paginatable;
use App\Models\ReadingGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReadingGroupResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ReadingGroup\StoreReadingGroupRequest;
use App\Http\Requests\ReadingGroup\UpdateReadingGroupRequest;

class ReadingGroupController extends Controller
{
    use Filterable;
    use Sortable;
    use Paginatable;
    public function __construct()
    {
        $this->middleware('permission:readingGroup-list', ['only' => ['index']]);
        $this->middleware('permission:readingGroup-create', ['only' => ['store']]);
        $this->middleware('permission:readingGroup-edit', ['only' => ['update']]);
        $this->middleware('permission:readingGroup-delete', ['only' => ['destroy']]);
        request()->headers->set('Accept', 'application/json');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        try {
            $readingGroupsQuery = ReadingGroup::query();

            // Apply filters (optional)
            $readingGroupsQuery = $this->applyFilters($readingGroupsQuery, $request,ReadingGroup::class);

            // Apply sorting (optional)
            $readingGroupsQuery = $this->applySort($readingGroupsQuery, $request,ReadingGroup::class);

            // Apply pagination
            $readingGroups = $this->applyPaginate($readingGroupsQuery, $request);

            return response()->json([
                'success' => true,
                'data' => ReadingGroupResource::collection($readingGroups),
                'pagination' => [
                    'total' => $readingGroups->total(),
                    'current_page' => $readingGroups->currentPage(),
                    'per_page' => $readingGroups->perPage(),
                    'last_page' => $readingGroups->lastPage(),
                    'has_more_pages' => $readingGroups->hasMorePages(),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pagination parameters',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            // Handle other exceptions
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReadingGroupRequest $request)
    {
        try {
            // Validate the request data
            $validated = $request->validated();

            // Create a new reading group instance
            $readingGroup = new ReadingGroup();

            // Fill the reading group attributes with validated data
            $readingGroup->fill($validated);

            // Save the reading group to the database
            $readingGroup->save();

            // Return success response with the created reading group
            return response()->json([
                'success' => true,
                'data' => new ReadingGroupResource($readingGroup),
            ], 201);
        } catch (ValidationException $e) {
            // Handle validation errors (optional)
            $errors = $e->errors();
            // You can customize the error response format here
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the reading group',
            ], 500);
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
        try {
            // Find the reading group by ID
            $readingGroup = ReadingGroup::findOrFail($id);

            // Return the reading group resource
            return response()->json([
                'success' => true,
                'data' => new ReadingGroupResource($readingGroup),
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle reading group not found (404)
            return response()->json([
                'success' => false,
                'message' => 'Reading group not found',
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the reading group',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReadingGroupRequest $request, $id)
    {
        try {
            // Find the reading group by ID
            $readingGroup = ReadingGroup::findOrFail($id);

            // Validate the request data
            $validated = $request->validated();

            // Fill the reading group attributes with validated data
            $readingGroup->fill($validated);

            // Save the updated reading group
            $readingGroup->save();

            // Return the updated reading group resource
            return response()->json([
                'success' => true,
                'data' => new ReadingGroupResource($readingGroup),
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle reading group not found (404)
            return response()->json([
                'success' => false,
                'message' => 'Reading group not found',
            ], 404);
        } catch (ValidationException $e) {
            // Handle validation errors (optional)
            $errors = $e->errors();
            // You can customize the error response format here
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the reading group',
            ], 500);
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
        try {
            // Find the readingGroup by ID
            $readingGroup = ReadingGroup::findOrFail($id);

            // Delete the readingGroup
            $readingGroup->delete();

            // Return success response with a message
            return response()->json([
                'success' => true,
                'message' => 'readingGroup deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle author not found error
            return response()->json([
                'success' => false,
                'message' => 'readingGroup not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the ReadingGroup'
            ], 500);
        }
    }

}
