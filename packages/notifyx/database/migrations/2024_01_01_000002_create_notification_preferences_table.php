<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // notification type (message, system, billing, etc.)
            $table->string('channel'); // delivery channel (database, mail, broadcast, etc.)
            $table->boolean('is_enabled')->default(true);
            $table->string('tenant_id')->nullable(); // Changed to string for better compatibility
            $table->timestamps();

            // Create a composite unique constraint that handles null tenant_id properly
            // We'll handle uniqueness in the application layer for better database compatibility
            $table->index(['user_id', 'type', 'channel', 'tenant_id'], 'notification_preferences_lookup');
            $table->index(['user_id', 'tenant_id'], 'notification_preferences_user_tenant');
            $table->index('type', 'notification_preferences_type');
            $table->index('channel', 'notification_preferences_channel');
            $table->index('tenant_id', 'notification_preferences_tenant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
