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
        'image'
    ];

    // Biar image_url ikut dikirim otomatis
    protected $appends = ['image_url', 'harga_int'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Accessor untuk menghasilkan URL gambar FULL.
     * Tidak memakai /storage karena Railway tidak support storage:link.
     * Folder gambar harus ada di: public/furniture/… atau public/uploads/…
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Jika image sudah berupa URL penuh, langsung return
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Pastikan tidak double slash
        $path = ltrim($this->image, '/');

        // Return URL file yang ada di public/
        return url($path);
    }

    /**
     * Convert harga ke integer agar Flutter tidak error saat parsing
     */
    public function getHargaIntAttribute()
    {
        if ($this->harga === null) {
            return 0;
        }

        // Convert harga apapun jenisnya (string/float/int)
        return (int) floatval($this->harga);
    }
}
