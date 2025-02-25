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
        Schema::create('tree3s', function (Blueprint $table) {
            $table->id();
            $table->string('tree3_name')->unique();
            $table->integer('tree3_code')->unique();
            $table->foreignId('tree2_id')->constrained()->cascadeOnDelete();
            $table->integer('tree2_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree3s');
    }
};
