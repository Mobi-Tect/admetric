<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (Schema::hasTable('chlidaccount')) {
			Schema::table('chlidaccount', function ($table) {
			});
		}else{
			Schema::create('chlidaccount', function (Blueprint $table) {
				$table->increments('id');
				//$table->integer('user_id')->index();
				$table->integer('account_id')->index();
				$table->integer('caccount_id')->index();
				$table->string('account_name');
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
        Schema::drop('chlidaccount');
    }
}
