<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "order_items";
    protected $fillable = [
        'material_id', 'product_id', 'set_id', 'order_id', 'amount', 'unit_price', 'total'
    ];
}
