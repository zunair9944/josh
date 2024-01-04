php a<?php

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
        Schema::create('appointment_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('icon')->default('general-eye-exam');
            $table->integer('length');
            $table->integer('limit');
            $table->integer('buffer_time')->nullable();
            $table->integer('beforeEventBuffer');
            $table->integer('afterEventBuffer');
            $table->json('users');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('questionnaire_id')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_types');
    }
};
