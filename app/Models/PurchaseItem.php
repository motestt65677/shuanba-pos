<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "purchase_items";
    protected $fillable = [
        'branch_id','purchase_id', 'material_id', 'amount', 'unit_price', 'total', 'note1'
    ];
}
