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

        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained('podcasts')->onDelete('cascade');
            $table->string('cover_image');
            $table->string('title');
            $table->string('slug');
            $table->string('description');
            $table->string('guest');
            $table->string('audio_file');
            $table->dateTime('release_date');
            $table->string('duration');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropForeign(['category_podcast_id']);
            $table->dropColumn('category_podcast_id');
        });
    }
};
