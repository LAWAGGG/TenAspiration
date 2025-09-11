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
        Schema::create('aspiration_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->constrained("events")->onDelete("cascade");
            $table->text("message");
            $table->enum("to", ["Divisi Keamanan", "Divisi Kebersihan", "Divisi Acara", "Divisi Dokumentasi", "Divisi Humas", "Divisi Perlengkapan", "lainnya"]);
            $table->string("other_to")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspiration_events');
    }
};
