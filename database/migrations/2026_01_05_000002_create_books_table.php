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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->unique();
            $table->string('title', 255);
            $table->string('author', 255);
            $table->string('publisher', 100)->nullable();
            $table->year('publication_year')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->text('description')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->unsignedInteger('total_copies')->default(1);
            $table->unsignedInteger('available_copies')->default(1);
            $table->string('language', 50)->default('Indonesian');
            $table->unsignedInteger('pages')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('title');
            $table->index('author');
            $table->index('category_id');
            
            // Fulltext index only for MySQL
            // For SQLite, we'll use LIKE queries instead
            // Uncomment below for MySQL production:
            // $table->fullText(['title', 'author', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
