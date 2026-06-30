<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->string('to', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->enum('to', ["wakil kesiswaan", "wakil sarpras", "wakil kurikulum", "wakil humas", "tata usaha", "OSIS", "MPK", "Ekskul", "umum"])->change();
        });
    }
};