<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
		if (Schema::hasTable('accounts')) {
			Schema::table('accounts', function ($table) {
				$table->integer('mccaccount_id');
			});
		}else{
			Schema::create('accounts', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->index();
				$table->string('name');
				$table->string('type')->default('Google');
				$table->integer('metric_id');
				$table->longText('token');
				$table->date('created');
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
        Schema::drop('accounts');
    }
}
