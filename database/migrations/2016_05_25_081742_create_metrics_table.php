<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (Schema::hasTable('metrics')) {
			Schema::table('metrics', function ($table) {
			});
		}else{
			Schema::create('metrics', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->index();
				$table->integer('account_id')->index();
				$table->integer('board_id');
				$table->integer('metric_value');
				$table->string('type')->default('Google');
				$table->integer('metric_id');
				$table->integer('set_aacount');
				$table->string('report');
				$table->string('date_time');
				$table->string('date_type');
				$table->timestamps();
			});
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('metrics');
    }
}
