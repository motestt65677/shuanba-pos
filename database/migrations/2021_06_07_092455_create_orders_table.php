<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prep_by')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('voucher_date');
            $table->string('order_no', 14);
            $table->string('payment_type', 10)->comment('付款方式 cash, monthly');
            $table->decimal('total', 13, 2)->nullable()->default(0);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('set_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('amount')->default(0);
            $table->decimal('unit_price', 13, 2)->default(0);
            $table->decimal('total', 13, 2)->nullable()->default(0);
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
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');

    }
}
