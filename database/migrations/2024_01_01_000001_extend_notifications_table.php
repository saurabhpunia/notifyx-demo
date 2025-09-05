<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend Laravel's notifications table (assumes it already exists)
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'channel')) {
                $table->string('channel')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'type_tag')) {
                $table->string('type_tag')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'tenant_id')) {
                $table->string('tenant_id')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['channel', 'type_tag', 'tenant_id', 'metadata']);
        });
    }
};
