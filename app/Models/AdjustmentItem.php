<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentItem extends Model
{
    use HasFactory;
    protected $table = "adjustment_items";
    protected $fillable = [
        'material_id', 'adjustment_id', 'amount', 'unit_price', 'total', 'adjustment_type'
    ];
}
