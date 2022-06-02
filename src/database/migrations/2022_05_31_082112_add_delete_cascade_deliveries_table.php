<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeleteCascadeDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // $table->dropForeign('answers_user_id_foreign');
            $table->foreignId('product_id')->onDelete('cascade')->change();
            $table->foreignId('store_id')->onDelete('cascade')->change();
            //$table->foreign('user_id')
            //->references('id')->on('users')
            //->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            //
        });
    }
}
