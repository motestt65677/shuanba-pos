<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosingItem extends Model
{
    use HasFactory;
    protected $table = "closing_items";
    protected $fillable = [
        'closing_id','material_id','purchase_count','purchase_total','order_count','order_total','order_cost',
        'closing_count','closing_total', 'purchase_unit_price', 'starting_total', 'starting_count','purchase_return_count', 'purchase_return_total'
    ];
}
