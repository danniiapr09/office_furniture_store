<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    use HasFactory;

    protected $table = 'furnitures';

    protected $fillable = [
        'nama',
        'category_id',
        'harga',
        'stok',
        'deskripsi',
        'image',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected $appends = ['image_url', 'images_url'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return url($this->image);
    }

    public function getImagesUrlAttribute()
    {
        if (!$this->images) return [];

        return array_map(function ($img) {
            return url($img);
        }, $this->images);
    }
}
