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
        Schema::create('tree2s', function (Blueprint $table) {
            $table->id();
            $table->string('tree2_name')->unique();
            $table->integer('tree2_code')->unique();
            $table->foreignId('tree1_id')->constrained()->cascadeOnDelete();
            $table->integer('tree1_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree2s');
    }
};
