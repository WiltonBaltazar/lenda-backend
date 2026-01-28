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
        Schema::disableForeignKeyConstraints();

        Schema::create('audiobooks', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('audiobook_serie_id')->constrained('audiobook_series')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('description');
            $table->string('cover_image');
            $table->date('year');
            $table->string('narrator');
            $table->string('duration')->nullable();
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobooks');
    }
};
