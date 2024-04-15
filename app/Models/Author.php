<?php

namespace App\Models;

use App\Models\Book;
use App\Traits\Sortable;
use App\Traits\Filterable;
use App\Traits\Paginatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model implements HasMedia
{
    use HasFactory;
    use Filterable;
    use Sortable;
    use Paginatable;
    use InteractsWithMedia;


   
    protected $fillable = [
        'name',
        'biography',
        'image',
    ];
    public const ValidSortFields = ['id','name','created_at','updated_at'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
   
}
