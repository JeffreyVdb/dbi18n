<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create i18n table
		Schema::create('i18n_keys', function (Blueprint $table)
		{
			$table->increments('id');
		});

		// Create locales table to hold different languages
		Schema::create('i18n_locales', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 16);
			$table->string('name', 50);
		});

		$translationCommon = function ($table)
		{
			$table->increments('id');
			$table->unsignedInteger('i18n_id');
			$table->unsignedInteger('locale_id');

			// Foreign keys
			$table->foreign('i18n_id')->references('id')->on('i18n_keys');
			$table->foreign('locale_id')->references('id')->on('i18n_locales');
		};

		// Create translations table with links to i18n keys and locales
		Schema::create('i18n_translations', function (Blueprint $table) use ($translationCommon)
		{
			$translationCommon($table);
			$table->string('translation', 128);
		});

		// Create translations table for texts
		Schema::create('i18n_translation_texts', function (Blueprint $table) use ($translationCommon)
		{
			$translationCommon($table);
			$table->text('translation');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('i18n_keys');
		Schema::drop('i18n_locales');

		// Drop foreign keys
		foreach (['i18n_translations', 'i18n_translation_texts'] as $tableName) {
			Schema::dropForeign($tableName . '_i18n_id_foreign');
			Schema::dropForeign($tableName . '_locale_id_foreign');

			Schema::drop($tableName);
		}
	}

}
