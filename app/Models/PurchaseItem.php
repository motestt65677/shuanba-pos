<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    protected $table = "purchase_items";
    protected $fillable = [
        'purchase_id', 'material_id', 'amount', 'unit_price', 'total', 'note1'
    ];
}
