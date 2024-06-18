<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 33cb8b7c-0069-4020-ba2a-0771e8796e39
 * Migration local datetime: 2024-06-17 19:48:30.475997
 * Migration UTC datetime: 2024-06-18 02:48:30.475997
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrations2024061719483047 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('creditcard_type')->nullable(true)->after('card_cvv');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('creditcard_type');
        });
    }
}