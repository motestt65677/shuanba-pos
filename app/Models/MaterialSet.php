<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialSet extends Model
{
    use HasFactory;
    protected $table = "material_sets";
    protected $fillable = [
        'supplier_id', 'material_id', 'set_unit_price', 'material_count', 'name'
    ];
}
