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
        Schema::create('tree4s', function (Blueprint $table) {
            $table->id();
            $table->string('tree4_name');
            $table->integer('tree4_code')->unique();
            $table->foreignId('tree3_id')->constrained()->cascadeOnDelete();
            $table->integer('tree3_code');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree4s');
    }
};
