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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->timestamp('request_date')->useCurrent();
            $table->timestamp('approval_date')->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('return_date')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'cancelled', 'active', 'returned', 'overdue'])->default('requested');
            $table->unsignedTinyInteger('renewal_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('user_id');
            $table->index('book_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
