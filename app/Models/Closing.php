<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Closing extends Model
{
    use HasFactory;
    protected $table = "closings";
    protected $fillable = [
        'year_month'
    ];
}
