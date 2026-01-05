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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->enum('type', ['content_based', 'collaborative', 'popularity', 'recency', 'hybrid']);
            $table->decimal('score', 5, 2); // 0.00 - 100.00
            $table->boolean('clicked')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'book_id', 'type']);
            $table->index('user_id');
            $table->index('book_id');
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
