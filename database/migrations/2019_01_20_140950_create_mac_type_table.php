<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMacTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mac_type', function(Blueprint $table)
		{
			$table->smallInteger('type_id', true)->unsigned();
			$table->string('type_name', 60)->default('')->index('type_name');
			$table->string('type_en', 60)->default('')->index('type_en');
			$table->smallInteger('type_sort')->unsigned()->default(0)->index('type_sort');
			$table->smallInteger('type_pid')->unsigned()->default(0)->index('type_pid');
			$table->boolean('type_status')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mac_type');
	}

}
