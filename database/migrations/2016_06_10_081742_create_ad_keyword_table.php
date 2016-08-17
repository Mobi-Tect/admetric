<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (Schema::hasTable('adkeyword')) {
			Schema::table('adkeyword', function ($table) {
			});
		}else{
			Schema::create('adkeyword', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->index();
				$table->integer('account_id')->index();
				$table->integer('caccount_id')->index();
				$table->integer('campaign_id')->index();
				$table->integer('adgroup_id')->index();
				$table->integer('keyword_id')->index();
				$table->string('keywords');
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
        Schema::drop('adkeyword');
    }
}
