<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdjustmentCountColumnToClosingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closing_items', function (Blueprint $table) {
            $table->decimal('adjustment_total', 13, 2)->after("order_total")->comment('調整金額')->nullable();
            $table->decimal('adjustment_count', 13, 2)->after("order_total")->comment('調整數量')->nullable();
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
            $table->dropColumn('adjustment_total');
            $table->dropColumn('adjustment_count');
        });
    }
}
