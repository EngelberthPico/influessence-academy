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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('type')->default('recorded')->after('vimeo_id');
            $table->unsignedInteger('session_count')->nullable()->after('type');
            $table->unsignedInteger('duration_months')->nullable()->after('session_count');
            $table->unsignedInteger('redemption_window_days')->nullable()->after('duration_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['type', 'session_count', 'duration_months', 'redemption_window_days']);
        });
    }
};
