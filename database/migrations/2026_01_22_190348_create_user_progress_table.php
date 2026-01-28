<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();

            // --- CHANGE THIS LINE ---
            // Old: $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // New: Use foreignUuid for UUID user IDs
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relation (keeps content_id as integer, which is correct for Ebooks/Audiobooks)
            $table->morphs('content');

            $table->string('progress_point');
            $table->integer('current_chapter_index')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'content_id', 'content_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
