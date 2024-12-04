s<?php

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
        Schema::create('vehicle_mileages', function (Blueprint $table) {
            $table->id('mileage_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('date');
            $table->integer('start_mileage');
            $table->integer('end_mileage');
            $table->string('location_start', 255);
            $table->string('location_end', 255);
            $table->string('route_description', 255);
            $table->integer('distance_traveled');
            $table->timestamps();
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_mileages');
    }
};
