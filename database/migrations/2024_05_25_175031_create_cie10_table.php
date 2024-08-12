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
        Schema::create('cie10', function (Blueprint $table) {
            $table->string('cie_code', length: 4)->primary();
            $table->text('cie_name');
            $table->string('cie_sex', length: 2);
            $table->integer('cie_limi');
            $table->integer('cie_limf');
            $table->integer('cie_mortality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cie10');
    }
};
