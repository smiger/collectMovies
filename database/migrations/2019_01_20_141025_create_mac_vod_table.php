<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMacVodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mac_vod', function(Blueprint $table)
		{
			$table->increments('vod_id');
			$table->smallInteger('type_id')->default(0)->index('type_id');
			$table->smallInteger('type_id_1')->unsigned()->default(0)->index('type_id_1');
			$table->smallInteger('group_id')->unsigned()->default(0)->index('group_id');
			$table->string('vod_name')->default('')->index('vod_name');
			$table->string('vod_sub')->default('');
			$table->string('vod_en')->default('')->index('vod_en');
			$table->boolean('vod_status')->default(0);
			$table->char('vod_letter', 1)->default('')->index('vod_letter');
			$table->string('vod_color', 6)->default('');
			$table->string('vod_tag', 100)->default('')->index('vod_tag');
			$table->string('vod_class')->default('')->index('vod_class');
			$table->string('vod_pic')->default('');
			$table->string('vod_pic_thumb')->default('');
			$table->string('vod_pic_slide')->default('');
			$table->string('vod_actor')->default('')->index('vod_actor');
			$table->string('vod_director')->default('')->index('vod_director');
			$table->string('vod_writer', 100)->default('');
			$table->string('vod_blurb')->default('');
			$table->string('vod_remarks', 100)->default('');
			$table->string('vod_pubdate', 100)->default('');
			$table->integer('vod_total')->unsigned()->default(0)->index('vod_total');
			$table->string('vod_serial', 20)->default('0');
			$table->string('vod_tv', 30)->default('');
			$table->string('vod_weekday', 30)->default('');
			$table->string('vod_area', 20)->default('')->index('vod_area');
			$table->string('vod_lang', 10)->default('')->index('vod_lang');
			$table->string('vod_year', 10)->default('')->index('vod_year');
			$table->string('vod_version', 30)->default('')->index('vod_version');
			$table->string('vod_state', 30)->default('')->index('vod_state');
			$table->string('vod_author', 60)->default('');
			$table->string('vod_jumpurl', 150)->default('');
			$table->string('vod_tpl', 30)->default('');
			$table->string('vod_tpl_play', 30)->default('');
			$table->string('vod_tpl_down', 30)->default('');
			$table->boolean('vod_isend')->default(0)->index('vod_isend');
			$table->boolean('vod_lock')->default(0)->index('vod_lock');
			$table->boolean('vod_level')->default(0)->index('vod_level');
			$table->smallInteger('vod_points_play')->unsigned()->default(0)->index('vod_points_play');
			$table->smallInteger('vod_points_down')->unsigned()->default(0)->index('vod_points_down');
			$table->integer('vod_hits')->unsigned()->default(0)->index('vod_hits');
			$table->integer('vod_hits_day')->unsigned()->default(0)->index('vod_hits_day');
			$table->integer('vod_hits_week')->unsigned()->default(0)->index('vod_hits_week');
			$table->integer('vod_hits_month')->unsigned()->default(0)->index('vod_hits_month');
			$table->string('vod_duration', 10)->default('');
			$table->integer('vod_up')->unsigned()->default(0)->index('vod_up');
			$table->integer('vod_down')->unsigned()->default(0)->index('vod_down');
			$table->decimal('vod_score', 3, 1)->unsigned()->default(0.0)->index('vod_score');
			$table->integer('vod_score_all')->unsigned()->default(0)->index('vod_score_all');
			$table->integer('vod_score_num')->unsigned()->default(0)->index('vod_score_num');
			$table->integer('vod_time')->unsigned()->default(0)->index('vod_time');
			$table->integer('vod_time_add')->unsigned()->default(0)->index('vod_time_add');
			$table->integer('vod_time_hits')->unsigned()->default(0);
			$table->integer('vod_time_make')->unsigned()->default(0)->index('vod_time_make');
			$table->smallInteger('vod_trysee')->unsigned()->default(0);
			$table->integer('vod_douban_id')->unsigned()->default(0);
			$table->decimal('vod_douban_score', 3, 1)->unsigned()->default(0.0);
			$table->string('vod_reurl')->default('');
			$table->string('vod_rel_vod')->default('');
			$table->string('vod_rel_art')->default('');
			$table->text('vod_content', 65535);
			$table->string('vod_play_from')->default('');
			$table->string('vod_play_server')->default('');
			$table->string('vod_play_note')->default('');
			$table->text('vod_play_url', 16777215);
			$table->string('vod_down_from')->default('');
			$table->string('vod_down_server')->default('');
			$table->string('vod_down_note')->default('');
			$table->text('vod_down_url', 16777215);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mac_vod');
	}

}
