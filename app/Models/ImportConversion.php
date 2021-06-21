<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportConversion extends Model
{
    use HasFactory;
    protected $table = "import_conversions";
    protected $fillable = [
        'supplier_id', 'material_id', 'import_price', 'import_unit', 'import_count', 'material_count'
    ];
}
