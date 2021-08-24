<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjustmentItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "adjustment_items";
    protected $fillable = [
        'material_id', 'adjustment_id', 'amount', 'unit_price', 'total', 'adjustment_type'
    ];
}
