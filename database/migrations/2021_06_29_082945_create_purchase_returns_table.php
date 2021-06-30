<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prep_by')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->date('voucher_date');
            $table->string('purchase_return_no', 12);
            // $table->string('payment_type', 10)->comment('付款方式 cash, monthly');
            $table->decimal('total', 13, 2)->nullable()->default(0);
            // $table->string('note1')->nullable();
            // $table->string('note2')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_return_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedBigInteger('purchase_item_id')->nullable();
            $table->decimal('amount', 13, 2)->default(0);
            $table->decimal('unit_price', 13, 2)->default(0);
            $table->decimal('total', 13, 2)->nullable()->default(0);
            // $table->string('note1')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('purchase_return_items');
    }
}
