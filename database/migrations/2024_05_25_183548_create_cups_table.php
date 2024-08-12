<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cups', function (Blueprint $table) {
            $table->string('cups_code', length: 10)->primary();
            $table->string('cups_name', length: 255);
            $table->text('cups_description');
            $table->string('cups_sex', length: 2);
            $table->string('cups_years_start', length: 4)->default('');
            $table->string('cups_years_end', length: 4)->default('');
            $table->bigInteger('cups_iss');
            $table->bigInteger('cups_soat');
            $table->bigInteger('cups_particular');
            $table->bigInteger('cups_other');
            $table->string('cups_file', length: 3);
            $table->integer('cups_ot_type');
            $table->string('cups_state', length: 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cups');
    }
};
