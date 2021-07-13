<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportMaterial extends Model
{
    use HasFactory;
    protected $table = "import_materials";
    protected $fillable = [
        'import_id', 'material_id', 'material_count'
    ];
}
