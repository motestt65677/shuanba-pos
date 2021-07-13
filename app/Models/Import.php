<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;
    protected $table = "imports";
    protected $fillable = [
        'import_no','name', 'description', 'price'
    ];

    public function importMaterials()
    {
        return $this->hasMany(ImportMaterial::class);
    }
}
