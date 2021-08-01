<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "purchases";
    protected $fillable = [
        'prep_by', 'branch_id', 'supplier_id', 'voucher_date', 'purchase_no', 'payment_type', 'total', 'note1', 'note2', 'is_paid'
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturns::class);
    }
}
