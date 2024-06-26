<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 17f4f064-38cb-40cd-b71e-d1b1a58a8bfe
 * Migration local datetime: 2024-06-17 20:01:58.626382
 * Migration UTC datetime: 2024-06-18 03:01:58.626382
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrations2024061720015862 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('card_cvv')->after('card_number')->change();
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
            $table->integer('card_cvv')->after('card_number')->change();
        });
    }
}
