<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $table = "materials";
    protected $fillable = [
        'supplier_id', 'material_no', 'name', 'big_category', 'med_category', 'unit', 'unit_price', 'stock'
    ];
}
