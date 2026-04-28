<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'stok',
        'price',
        'category',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
