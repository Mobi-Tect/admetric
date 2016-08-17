<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (Schema::hasTable('adgroups')) {
			Schema::table('adgroups', function ($table) {
			});
		}else{
			Schema::create('adgroups', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->index();
				$table->integer('account_id')->index();
				$table->integer('caccount_id')->index();
				$table->integer('campaign_id')->index();
				$table->integer('adgroup_id')->index();
				$table->string('adgroup_name');
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
        Schema::drop('adgroups');
    }
}
