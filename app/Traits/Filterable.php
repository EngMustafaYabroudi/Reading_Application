<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function filterWhere(Builder $query, Request $request, $model): Builder
    {
        if ($request->has('filters')) {
            $filters = $request->get('filters');
            if (is_array($filters) && !empty($filters)) {
                $filterFields = $model::FILTERS; // Get model-specific filters
                foreach ($filters as $field => $value) {
                    if (in_array($field, $filterFields)) {
                        if ($field === 'book_title') {
                            $this->filterByBook($query, $value);
                        } elseif ($field === 'user_name') {
                            $this->filterByUser($query, $value);
                        } else {
                            $query->where($field, 'like', "%$value%");
                        }
                    }
                }
            }
        }

        return $query;
    }
    private function filterByUser(Builder $query, $userName): void
    {
        $query->whereHas('user', function (Builder $query) use ($userName) {
            $query->where('name', 'like', "%$userName%");
        });
    }
    private function filterByBook(Builder $query, $bookTitle): void
    {
        $query->whereHas('book', function (Builder $query) use ($bookTitle) {
            $query->where('title', 'like', "%$bookTitle%");
        });
    }
    public function applyCoreFilters(Builder $query, Request $request, Model $model): Builder
    {
        // ... Add your core filtering logic here

        if ($request->has('user_name')) {
            $userName = $request->get('user_name');
            $query->where('user_name', 'like', "%$userName%");
        }

        if ($request->has('book_title')) {
            $bookTitle = $request->get('book_title');
            $query->where('book_title', 'like', "%$bookTitle%");
        }

        // ... Add other core filtering logic as needed

        return $query;
    }

    public function applyDateRangeFilters(Builder $query, Request $request, $model): Builder
    {
        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        return $query;
    }
    public function applyRelationshipFilters(Builder $query, Request $request, Model $model): Builder
    {
        if ($request->has('author_name')) {
            $authorName = $request->get('author_name');
            $query->whereHas('author', function (Builder $query) use ($authorName) {
                $query->where('name', 'like', "%$authorName%");
            });
        }

        // ... Add other relationship filtering logic as needed

        return $query;
    }
    public function applyMultiValueFilters(Builder $query, Request $request, Model $model): Builder
    {
        if ($request->has('categories')) {
            $categories = $request->get('categories');
            $query->whereIn('category_id', $categories);
        }

        // ... Add other multi-value filtering logic as needed

        return $query;
    }


    public function applyFilters(Builder $query, Request $request, $model): Builder
    {
        $query = $this->filterWhere($query, $request, $model);

        // ... (Add other filtering logic as needed)

        return $query;
    }

}
