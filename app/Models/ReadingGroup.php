<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'book_id',
        'start_date',
        'end_date',
    ];
    public const ValidSortFields = ['id','name','created_at','updated_at'];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
