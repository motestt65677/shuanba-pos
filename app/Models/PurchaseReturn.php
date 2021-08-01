<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "purchase_returns";
    protected $fillable = [
        'prep_by', 'branch_id', 'purchase_id', 'supplier_id','voucher_date', 'purchase_return_no', 'total'
    ];
}
