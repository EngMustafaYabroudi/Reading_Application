<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface FilterableInterface
{
    public function filterWhere(Builder $query, Request $request,  $model): Builder;
    public function applyFilters(Builder $query, Request $request, $model): Builder;

}