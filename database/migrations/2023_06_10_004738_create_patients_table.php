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
        Schema::disableForeignKeyConstraints();
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('address_1');
            $table->string('address_2');
            $table->string('state');
            $table->string('phone');
            $table->string('zip');
            $table->string('country');
            $table->unsignedInteger('insurance_provider');
            $table->string('insurance_policy_number');
            $table->string('insurance_group_number');
            $table->dateTime('last_appointment')->nullable();
            $table->dateTime('next_appointment')->nullable();
            $table->json('prescription_values')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('insurance_provider')->references('id')->on('insurance_providers');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
