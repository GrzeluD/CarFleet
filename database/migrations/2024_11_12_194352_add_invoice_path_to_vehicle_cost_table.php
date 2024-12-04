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
        if (!Schema::hasColumn('vehicle_costs', 'invoice_path')) {
            Schema::table('vehicle_costs', function (Blueprint $table) {
                $table->string('invoice_path')->nullable()->after('vat_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_costs', function (Blueprint $table) {
            $table->dropColumn('invoice_path');
        });
    }
};
