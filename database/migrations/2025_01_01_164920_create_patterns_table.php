<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patterns', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid()->default(DB::raw('(gen_random_uuid())'));
            $table->foreignIdFor(\App\Models\User::class)->nullable();
            $table->json('pixels')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('color_count')->nullable();
            $table->string('original_image_path');
            $table->text('base64_pattern_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patterns');
    }
};
