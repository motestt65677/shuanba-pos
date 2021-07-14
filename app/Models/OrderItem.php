<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = "order_items";
    protected $fillable = [
        'material_id', 'product_id', 'set_id', 'order_id', 'amount', 'unit_price', 'total'
    ];
}
