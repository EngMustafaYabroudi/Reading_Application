<?php

namespace App\Traits;// Adjust the namespace as needed

use Illuminate\Http\Request;
use InvalidArgumentException;


trait Sortable
{
    /**
     * Applies sorting to the query based on request parameters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applySort($query, Request $request,$model)
    {
        if ($request->has('sort')) {
            $sortField = $request->get('sort');
            $sortDirection = $request->has('sort_direction') ? $request->get('sort_direction') : 'asc';

            $validSortFields = $model::ValidSortFields; // Access the constant from the model instance

            if (in_array($sortField,  $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                throw new InvalidArgumentException("Invalid sort field: '$sortField'");
            }
        }

        return $query;
    }

    /**
     * Defines the valid sort fields for the model.
     * Override this method in your model to customize allowed fields.
     *
     * @return array
     */
    // protected function getValidSortFields()
    // {
    //     return ['id','rating','name','title', 'created_at', 'updated_at']; // Example valid fields
    // }
}
