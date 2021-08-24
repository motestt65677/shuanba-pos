<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prep_by')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('voucher_date');
            $table->string('adjustment_no', 14);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('adjustment_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('adjustment_id')->nullable();
            $table->decimal('amount', 13, 2)->default(0);
            $table->decimal('unit_price', 13, 2)->default(0);
            $table->decimal('total', 13, 2)->nullable()->default(0);
            $table->string('adjustment_type', 10)->comment('increase, decrease');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustments');
        Schema::dropIfExists('adjustment_items');
    }
}
