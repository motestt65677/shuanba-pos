<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = "purchases";
    protected $fillable = [
        'prep_by', 'branch_id', 'supplier_id', 'voucher_date', 'purchase_no', 'payment_type', 'total', 'note1', 'note2', 'is_paid'
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }
}
