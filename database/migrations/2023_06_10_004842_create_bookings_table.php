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

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('appointment_type_id');
            $table->tinyInteger('rescheduled')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('status');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('reserved_frame_id');
            $table->json('intake_questionnaire_answers');
            $table->string('gcal_event_id')->nullable();
            $table->string('ol_event_id')->nullable();
            $table->string('ical_event_id')->nullable();
            $table->unsignedBigInteger('notification_id');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('appointment_type_id')->references('id')->on('appointment_types');
            // $table->foreign('notification_id')->references('id')->on('notifications');
        });
        Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
