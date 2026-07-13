<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspiration_events', function (Blueprint $table) {
            $table->text('message')->nullable()->change();
            $table->text('kesan_pesan')->nullable()->change();
            $table->text('perubahan_dari_event')->nullable()->change();
        });

        Schema::table('aspiration_keluh_kesah', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->change();
            $table->text('keluh_kesah')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('aspiration_events', function (Blueprint $table) {
            $table->text('message')->nullable(false)->change();
            $table->text('kesan_pesan')->nullable(false)->change();
            $table->text('perubahan_dari_event')->nullable(false)->change();
        });

        Schema::table('aspiration_keluh_kesah', function (Blueprint $table) {
            $table->string('phone_number')->nullable(false)->change();
            $table->text('keluh_kesah')->nullable(false)->change();
        });
    }
};
