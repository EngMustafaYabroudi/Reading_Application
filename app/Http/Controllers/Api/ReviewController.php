<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Review;
use App\Traits\Sortable;
use App\Traits\Filterable;
use App\Traits\Paginatable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Validator;
use App\Http\Resources\ReviewResource;
use App\Interfaces\FilterableInterface;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReviewController extends Controller
{
    use Filterable;
    use Sortable;
    use Paginatable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            // Build query with filters and sorting (optional)
            $reviewsQuery = Review::query();
            $reviewsQuery = $this->applyFilters($reviewsQuery, $request, Review::class); // Apply filters using the trait method (implement this method)
            $reviewsQuery = $this->applySort($reviewsQuery, $request, Review::class); // Apply sorting using the trait method (implement this method)
            $reviews = $this->applyPaginate($reviewsQuery, $request);


            // Return JSON response with reviews and pagination data
            return response()->json([
                'success' => true,
                'data' => ReviewResource::collection($reviews),
                'pagination' => [
                    'total' => $reviews->total(),
                    'current_page' => $reviews->currentPage(),
                    'per_page' => $reviews->perPage(),
                    'last_page' => $reviews->lastPage(),
                    'has_more_pages' => $reviews->hasMorePages(),
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            // Validate the request data
            $validated = $request->validated();
            $existingReview = Review::where('user_id', $validated['user_id'])
            ->where('book_id', $validated['book_id'])
            ->first();

            if ($existingReview) {
                // Existing review found, display message and return error response
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this book.',
                ], 422);
            }

            // Create a new reading group instance
            $review = new Review();

            // Fill the reading group attributes with validated data
            $review->fill($validated);

            // Save the reading group to the database
            $review->save();

            // Return success response with the created reading group
            return response()->json([
                'success' => true,
                'data' => new ReviewResource($review),
            ], 201);
        } catch (ValidationException $e) {
            // Handle validation errors with a more informative message (optional)
            $errors = $e->errors();
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this book.', // More specific message
                'errors' => $errors,
            ], 422);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the Review',
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
            $review = Review::findOrFail($id);

            // Return the reading group resource
            return response()->json([
                'success' => true,
                'data' => new ReviewResource($review),
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle reading group not found (404)
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the review',
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
    public function update(UpdateReviewRequest $request, $id)
    {
        try {
            // Find the reading group by ID
            $review = Review::findOrFail($id);

            // Validate the request data
            $validated = $request->validated();

            // Fill the reading group attributes with validated data
            $review->fill($validated);

            // Save the updated reading group
            $review->save();

            // Return the updated reading group resource
            return response()->json([
                'success' => true,
                'data' => new ReviewResource($review),
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
            $review = Review::findOrFail($id);

            // Delete the readingGroup
            $review->delete();

            // Return success response with a message
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle author not found error
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            report($e);  // Optionally report the exception for logging or debugging

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the Review'
            ], 500);
        }
    }

}
