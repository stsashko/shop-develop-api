<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'store_id', 'purchase_date', 'created_at', 'updated_at'];
}
