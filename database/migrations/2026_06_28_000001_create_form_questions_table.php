<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->enum('form_type', ['event', 'audiensi', 'keluh_kesah']);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('question_key');
            $table->string('question_label');
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['form_type', 'entity_id', 'question_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_questions');
    }
};