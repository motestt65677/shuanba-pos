<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDeleteColumnToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('order_items', function (Blueprint $table) {
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
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
