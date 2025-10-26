<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'category_id',
        'image',
    ];

    // Relasi ke kategori (optional)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
