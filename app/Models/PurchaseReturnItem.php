<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturnItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "purchase_return_items";
    protected $fillable = [
        'purchase_return_id', 'purchase_item_id', 'amount', 'unit_price', 'total', 'material_id'
    ];
}
