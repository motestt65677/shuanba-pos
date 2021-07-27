<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('id');
        });
        Schema::table('closings', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn("branch_id");
        });
        Schema::table('closings', function (Blueprint $table) {
            $table->dropColumn("branch_id");
        });
    }
}
