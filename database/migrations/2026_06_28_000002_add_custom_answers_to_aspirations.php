<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspiration_events', function (Blueprint $table) {
            $table->json('custom_answers')->nullable()->after('perubahan_dari_event');
        });

        Schema::table('aspirations', function (Blueprint $table) {
            $table->json('custom_answers')->nullable()->after('message');
        });

        Schema::table('aspiration_keluh_kesah', function (Blueprint $table) {
            $table->json('custom_answers')->nullable()->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('aspiration_events', function (Blueprint $table) {
            $table->dropColumn('custom_answers');
        });

        Schema::table('aspirations', function (Blueprint $table) {
            $table->dropColumn('custom_answers');
        });

        Schema::table('aspiration_keluh_kesah', function (Blueprint $table) {
            $table->dropColumn('custom_answers');
        });
    }
};