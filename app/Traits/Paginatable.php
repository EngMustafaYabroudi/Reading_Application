<?php

namespace App\Traits;// Adjust the namespace as needed


use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;

trait Paginatable
{
    /**
     * Applies pagination to a query builder or Eloquent query.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param int $defaultPerPage (optional) The default number of items per page. Defaults to 10.
     * @param string $pageName (optional) The query string parameter name for the current page. Defaults to 'page'.
     * @return \Illuminate\Pagination\LengthAwarePaginator
     *
     * @throws \Illuminate\Validation\ValidationException if pagination parameters are invalid
     */
    public function applyPaginate($query, Request $request, int $defaultPerPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        $validator = Validator::make($request->all(), [
            $pageName => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100', // Adjust max per_page limit as needed
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $page = $request->get($pageName, 1);
        $perPage = $request->get('per_page', $defaultPerPage);

        return $query->paginate($perPage, ['*'], $pageName, $page);
    }
}
