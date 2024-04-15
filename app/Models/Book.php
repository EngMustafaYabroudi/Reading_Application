<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'description',
        'genre',
        'published_year',
        'image',
    ];
    public function readingGroups()
    {
        return $this->hasMany(ReadingGroup::class);
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
