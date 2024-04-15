<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use PHPUnit\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:book-list', ['only' => ['index']]);
        $this->middleware('permission:book-create', ['only' => ['store']]);
        $this->middleware('permission:book-edit', ['only' => ['update']]);
        $this->middleware('permission:book-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Optionally apply filters and sorting

            $books = Book::query();

            // Apply filters if provided in the request

            if ($request->has('filter')) {
                $books = $books->where($request->filter);
            }

            if ($request->has('sort')) {
                $books = $books->orderBy($request->sort);
            }

            // Apply eager loading for related authors (optional)
            // $books = $books->with('author');

            $books = $books->paginate(10); // Adjust page size as needed

            // Return success response with the paginated books
            return response()->json([
                'success' => true,
                'data' => BookResource::collection($books)
            ]);
        } catch (Exception $e) {
            // Catch any unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the books'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        try {

            // Validate the request data
            $validated = $request->validated();

            // Create a new book instance
            $book = new Book();

            // Fill the book attributes with validated data
            $book->fill($validated);

            // Store the image (if present) and update the model's image attribute
            if (isset($validated['image'])) {
                try {
                    $image = $validated['image'];
                    $imageName = $this->storeImage($image, "public", " Books"); // Custom function to handle image storage
                    $book->image = $imageName;
                } catch (Exception $e) {
                    // Handle image storage errors
                    $errorMessage = $e->getMessage();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error while storing image: ' . $errorMessage
                    ], 500);
                }
            }
            // Save the book to the database
            $book->save();

            // Return success response with the created book
            return response()->json([
                'success' => true,
                'data' => new BookResource($book)
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
                'message' => 'An error occurred while creating the book'
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
            // Find the book by ID
            $book = Book::findOrFail($id);

            // Return success response with the book data
            return response()->json([
                'success' => true,
                'data' => new BookResource($book)
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle book not found error
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the book'
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
    public function update(UpdateBookRequest $request, $id)
    {
        try {
            // Find the book by ID
            $book = Book::findOrFail($id);

            // Validate the request data
            $validated = $request->validated();
            

            // Update the book's attributes
            $book->fill($validated);

            // Save the book to the database
            $book->save();

            // Return success response with the updated book
            return response()->json([
                'success' => true,
                'data' => new BookResource($book)
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Handle book not found error
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
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
                'message' => 'An error occurred while updating the book'
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
            // Find the book by ID
            $book = Book::findOrFail($id);

            // Delete the book
            $book->delete();

            // Return success response with a message
            return response()->json([
                'success' => true,
                'message' => 'Book deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle book not found error
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the book'
            ], 500);
        }
    }

}
