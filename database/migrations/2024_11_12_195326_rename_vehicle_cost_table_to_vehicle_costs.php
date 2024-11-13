<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('vehicle_cost', 'vehicle_costs');
    }

    public function down()
    {
        Schema::rename('vehicle_costs', 'vehicle_cost');
    }
};
