<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase_items extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_id', 'product_count', 'product_price'];
}
