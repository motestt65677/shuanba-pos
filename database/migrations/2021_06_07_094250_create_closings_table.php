<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('year_month');
            $table->timestamps();
        });

        Schema::create('closing_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('closing_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('purchase_count', 13, 2)->comment('進貨數量')->nullable();
            $table->decimal('purchase_total', 13, 2)->comment('進貨金額')->nullable();
            $table->decimal('order_count', 13, 2)->comment('銷貨數量')->nullable();
            $table->decimal('order_total', 13, 2)->comment('銷貨金額')->nullable();
            $table->decimal('order_cost', 13, 2)->comment('銷貨成本')->nullable();
            $table->decimal('closing_count', 13, 2)->comment('期末數量')->nullable();
            $table->decimal('closing_total', 13, 2)->comment('期末金額 = purchase_total - order_cost')->nullable();
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
        Schema::dropIfExists('closings');
        Schema::dropIfExists('closing_items');
    }
}
