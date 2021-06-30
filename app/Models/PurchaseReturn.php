<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    protected $table = "purchase_returns";
    protected $fillable = [
        'prep_by', 'branch_id', 'purchase_id', 'supplier_id','voucher_date', 'purchase_return_no', 'total'
    ];
}
