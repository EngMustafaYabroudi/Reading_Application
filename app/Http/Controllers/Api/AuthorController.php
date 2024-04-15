<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use PHPUnit\Exception;
use App\Traits\Sortable;
use App\Traits\Filterable;
use App\Traits\ImageUpload;
use App\Traits\Paginatable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorController extends Controller
{
    use Filterable;
    use Sortable;
    use Paginatable;
    use ImageUpload;
    public function __construct()
    {
        $this->middleware('permission:author-list', ['only' => ['index']]);
        $this->middleware('permission:author-create', ['only' => ['store']]);
        $this->middleware('permission:author-edit', ['only' => ['update']]);
        $this->middleware('permission:author-delete', ['only' => ['destroy']]);
        request()->headers->set('Accept', 'application/json');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Validate pagination parameters (optional)
            $validator = Validator::make($request->all(), [
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100', // Adjust max per_page limit as needed
            ]);

            if ($validator->fails()) {
                throw new HttpException(422, 'Invalid pagination parameters');
            }

            // Build query with filters and sorting (optional)
            $authorsQuery = Author::query();
            $authorsQuery = $this->applyFilters($authorsQuery, $request, Author::class); // Apply filters using the trait method
            $authorsQuery = $this->applySort($authorsQuery, $request, Author::class); // Apply sorting using the trait

            // Apply pagination with default values if not provided
            // $page = $request->get('page', 1);
            // $perPage = $request->get('per_page', 5); // Adjust default per_page limit as needed

            // $authors = $authorsQuery->paginate($perPage, ['*'], 'page', $page);
            $authors = $this->applyPaginate($authorsQuery, $request, 15, 'authors_per_page'); // Adjust default and page name

            return response()->json([
                'success' => true,
                'data' => AuthorResource::collection($authors),
                'pagination' => [
                    'total' => $authors->total(),
                    'current_page' => $authors->currentPage(),
                    'per_page' => $authors->perPage(),
                    'last_page' => $authors->lastPage(),
                    'has_more_pages' => $authors->hasMorePages(),
                ],
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (Exception $e) {
            report($e); // Optionally log the exception for debugging
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }


    public function store(StoreAuthorRequest $request)
    {
        try {
            // Validate the request data
            $validated = $request->validated();

            // Create a new author instance
            $author = new Author();


            // Fill the author attributes with validated data
            $author->fill($validated);

            // Store the image (if present) and update the model's image attribute
            if (isset($validated['image'])) {
                try {
                    $image = $validated['image'];
                    $imageName = $this->storeImage($image, "public", "Authors/"); // Custom function to handle image storage
                    $author->image = $imageName;
                } catch (Exception $e) {
                    // Handle image storage errors
                    $errorMessage = $e->getMessage();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error while storing image: ' . $errorMessage
                    ], 500);
                }
            }


            // Save the author to the database
            $author->save();

            // Return success response with the created author
            return response()->json([
                'success' => true,
                'data' => new AuthorResource($author)
            ], 201);
        } catch (ValidationException $e) {
            // Handle validation errors (optional)
            $errors = $e->errors();
            // You can customize the error response format here
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the author'
            ], 500);
        }
    }

    public function update(UpdateAuthorRequest $request, $id)
    {
        try {
            // Find the author by ID
            $author = Author::findOrFail($id);
            // Validate the request data
            $validated = $request->validated();

            if (isset($validated['image'])) {
                // Handle image update
                try {
                    // If the original image exists, delete it
                    $this->deleteImage($author->image, "public", "Authors");
                } catch (Exception $e) {
                    // Handle image storage errors
                    $errorMessage = $e->getMessage();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error while storing image: ' . $errorMessage
                    ], 500);
                }
            }

            // Update the author's attributes
            $author->fill($validated);
            // Check if a new image has been uploaded
            if (isset($validated['image'])) {
                // Handle image update

                try {
                    // If the original image exists, delete it
                    $newImageName = $this->storeImage($validated['image'], "public", "Authors/");
                    // Update the author's image attribute with the new filename
                    $author->image = $newImageName;
                } catch (Exception $e) {
                    // Handle image storage errors
                    $errorMessage = $e->getMessage();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error while storing image: ' . $errorMessage
                    ], 500);
                }
            }

            // Save the author to the database
            $author->save();
            // Return success response with the updated author
            return response()->json([
                'success' => true,
                'data' => new AuthorResource($author)
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Handle author not found error
            return response()->json([
                'success' => false,
                'message' => 'Author not found'
            ], 404);
        } catch (ValidationException $e) {
            // Handle validation errors (optional)
            $errors = $e->errors();
            // You can customize the error response format here
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the author'
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            // Find the author by ID
            $author = Author::with('books')->findOrFail($id);

            // Return success response with the author data
            return response()->json([
                'success' => true,
                'data' => new AuthorResource($author)
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle author not found error
            return response()->json([
                'success' => false,
                'message' => 'Author not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the author'
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            // Find the author by ID
            $author = Author::findOrFail($id);

            // Delete the author
            $author->delete();

            // Return success response with a message
            return response()->json([
                'success' => true,
                'message' => 'Author deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle author not found error
            return response()->json([
                'success' => false,
                'message' => 'Author not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the author'
            ], 500);
        }
    }


}
