<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 96801efe-5764-4af1-98ac-e9236e3ff51f
 * Migration local datetime: 2024-06-15 20:34:45.845352
 * Migration UTC datetime: 2024-06-16 03:34:45.845352
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrations2024061520344584 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('zip')->nullable(true)->after('state')->change();
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
            $table->smallInteger('zip')->nullable(true)->after('state')->change();
        });
    }
}