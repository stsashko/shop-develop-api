<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'product_name', 'image', 'price', 'category_id', 'manufacturer_id', 'created_at', 'updated_at'];
}
