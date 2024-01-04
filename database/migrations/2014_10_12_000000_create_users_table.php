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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->dateTime('emailVerified')->nullable();
            $table->string('password');
            $table->string('role')->default('store_owner');
            $table->unsignedBigInteger('selectedCalendars')->nullable();
            $table->tinyInteger('away')->nullable();
            $table->string('avatar_url')->nullable();
            $table->tinyInteger('completedOnBoarding')->default(0);
            $table->string('access_type')->default('all');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
