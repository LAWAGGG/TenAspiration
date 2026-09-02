<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_questions', function (Blueprint $table) {
            $table->enum('question_type', ['essay', 'pilihan_ganda', 'checkbox'])
                ->default('essay')
                ->after('question_label');
            $table->json('question_options')
                ->nullable()
                ->after('question_type');
        });
    }

    public function down(): void
    {
        Schema::table('form_questions', function (Blueprint $table) {
            $table->dropColumn(['question_type', 'question_options']);
        });
    }
};
