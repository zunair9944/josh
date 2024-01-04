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

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('created_by');
            $table->json('users');
            $table->string('shopify_store_public_key')->nullable();
            $table->string('shopify_store_private_key')->nullable();
            $table->unsignedBigInteger('notices')->nullable(); // Change the data type
            $table->string('logo_file')->nullable();
            $table->timestamps();
        
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('notices')->references('id')->on('notices')->nullable(); // Make sure it's nullable
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
