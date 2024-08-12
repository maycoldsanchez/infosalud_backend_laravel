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
        Schema::create('health_entities', function (Blueprint $table) {
            $table->string('enti_code', length: 6)->primary();
            $table->string('enti_nit', length: 12);
            $table->integer('enti_dv');
            $table->integer('enti_codtype');  
            $table->string('enti_name', length: 120);
            $table->string('enti_acronym', length: 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_entities');
    }
};
