<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 06151945-d165-4eff-a51a-96a871a63af8
 * Migration local datetime: 2024-06-15 19:32:11.968101
 * Migration UTC datetime: 2024-06-16 02:32:11.968101
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrations2024061519321196 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_files', function (Blueprint $table) {
            $table->integer('total_leads')->nullable(true)->default(0)->after('data');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_files', function (Blueprint $table) {
            $table->dropColumn('total_leads');
        });
    }
}
