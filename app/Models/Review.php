<?php

namespace App\Models;

use App\Traits\Sortable;
use App\Traits\Filterable;
use App\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    use Filterable;
    use Sortable;
    use Paginatable;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'review',
    ];

    public const ValidSortFields = ['id','review','rating','created_at','updated_at'];
    public const FILTERS = ['id','user_id','user_name','book_title','review','rating','created_at','updated_at'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

}
