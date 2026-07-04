<?php

use App\Models\TargetEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        TargetEmail::insert([
            ['email' => 'ahmadfagih.arrifai@gmail.com', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'yunitakamali72@gmail.com', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'mujahidrobbanisholahudin@gmail.com', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'desita1412@gmail.com', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sayutiazwarmi67@gmail.com', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('target_emails');
    }
};
