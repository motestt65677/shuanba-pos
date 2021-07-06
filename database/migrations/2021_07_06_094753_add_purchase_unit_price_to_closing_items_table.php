<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseUnitPriceToClosingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closing_items', function (Blueprint $table) {
            //
            $table->decimal('purchase_unit_price', 13, 2)->comment('進貨單價')->nullable()->after('order_cost');
            $table->decimal('starting_total', 13, 2)->comment('期初金額')->nullable()->after('closing_total');
            $table->decimal('starting_count', 13, 2)->comment('期初數量')->nullable()->after('closing_total');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closing_items', function (Blueprint $table) {
            //
            $table->dropColumn('purchase_unit_price');
            $table->dropColumn('starting_total');
            $table->dropColumn('starting_count');

        });
    }
}
