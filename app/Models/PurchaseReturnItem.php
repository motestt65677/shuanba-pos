<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;
    protected $table = "purchase_return_items";
    protected $fillable = [
        'purchase_return_id', 'purchase_item_id', 'amount', 'unit_price', 'total'
    ];
}
