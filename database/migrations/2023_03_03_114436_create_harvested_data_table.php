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
        Schema::create('harvested_data', function (Blueprint $table) {
            $table->id();
            $table->string('job_board');
            $table->string('search_area');
            $table->string('recruiter');
            $table->string('job_title');
            $table->string('job_location');
            $table->string('reference');
            $table->string('job_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvested_data');
    }
};
