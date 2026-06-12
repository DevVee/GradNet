<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Named app_notifications to avoid collision with Laravel's built-in notifications table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // recipient
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete(); // who triggered
            $table->foreignId('post_id')->nullable()->constrained('posts')->cascadeOnDelete();
            // type: reaction | comment | connection | news | event
            $table->string('type');
            $table->string('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
