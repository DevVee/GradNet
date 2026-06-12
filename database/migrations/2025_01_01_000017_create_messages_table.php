<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            // content_type: text (extendable to image/file later)
            $table->string('content_type')->default('text');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('seen_at')->nullable();
            // Self-referencing FK for reply threading
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
