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
        Schema::create('vehicle_cost', function (Blueprint $table) {
            $table->id('cost_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->dateTime('date');
            $table->unsignedBigInteger('cost_type_id');
            $table->string('description', 255);
            $table->float('amount_gross');
            $table->float('vat_rate');
            $table->float('amount_net');
            $table->float('vat_amount');
            $table->timestamps();
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('cost_type_id')->references('cost_type_id')->on('cost_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_cost');
    }
};
