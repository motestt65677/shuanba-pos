<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adjustment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "adjustments";
    protected $fillable = [
        'prep_by', 'branch_id', 'voucher_date', 'adjustment_no', 'note'
    ];

    public function adjustmentItems()
    {
        return $this->hasMany(AdjustmentItem::class);
    }


}
