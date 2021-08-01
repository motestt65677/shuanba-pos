<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "orders";
    protected $fillable = [
        'prep_by', 'branch_id', 'voucher_date', 'order_no', 'payment_type', 'total'
    ];
}
