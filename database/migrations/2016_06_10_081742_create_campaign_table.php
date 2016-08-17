<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (Schema::hasTable('campaign')) {
			Schema::table('campaign', function ($table) {
			});
		}else{
			Schema::create('campaign', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->index();
				$table->integer('account_id')->index();
				$table->integer('caccount_id')->index();
				$table->integer('campaign_id')->index();
				$table->string('campaign_name');
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
        Schema::drop('campaign');
    }
}
