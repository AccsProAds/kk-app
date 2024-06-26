<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: fe662edc-5c14-4de8-9165-b0013fd2b074
 * Migration local datetime: 2024-06-18 15:47:07.663695
 * Migration UTC datetime: 2024-06-18 22:47:07.663695
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrations2024061815470766 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_files', function (Blueprint $table) {
            $table->boolean('is_processing')->nullable(true)->default(0)->after('processed');
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
            $table->dropColumn('is_processing');
        });
    }
}
