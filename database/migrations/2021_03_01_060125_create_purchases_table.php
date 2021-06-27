<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prep_by')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('voucher_date');
            $table->string('purchase_no', 12);
            $table->string('payment_type', 10)->comment('付款方式 cash, monthly');
            $table->decimal('total', 13, 2)->nullable()->default(0);
            $table->string('note1')->nullable();
            $table->string('note2')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('amount', 13, 2)->default(0);
            $table->decimal('unit_price', 13, 2)->default(0);
            $table->decimal('total', 13, 2)->nullable()->default(0);
            $table->string('note1')->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('material_no', 5);
            $table->string('name');
            $table->string('big_category')->nullable();
            $table->string('med_category')->nullable();
            $table->string('unit', 100);
            $table->decimal('unit_price', 13, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier_no', 4);
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('cellphone', 20)->nullable();
            $table->string('tax_id', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('note1')->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
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
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('branches');

    }
}
