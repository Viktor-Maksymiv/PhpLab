<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('age_rating', 10);
            $table->date('release_date');
            $table->string('poster_url')->nullable();
            $table->timestamps();

            $table->index('release_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
