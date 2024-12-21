<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\CostType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleCostControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $vehicle;
    private $costType;

    protected function setUp(): void
    {
        parent::setUp();

        // Tworzymy użytkownika admina
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'adminTest@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Tworzymy pojazd
        $this->vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '12345678901234567',
            'production_year' => 2022,
            'license_plate' => 'ABC123',
            'insurance_expiry' => now()->addYear()->toDateString(),
            'inspection_due' => now()->addYear()->toDateString(),
        ]);

        // Tworzymy typ kosztu
        $this->costType = CostType::create([
            'cost_type_name' => 'Serwis',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_for_vehicle_cost()
    {
        $response = $this->actingAs($this->admin)
            ->post('/vehicle-costs', []);

        $response->assertSessionHasErrors([
            'vehicle_id',
            'date',
            'cost_type_id',
            'amount_gross',
            'vat_rate',
            'amount_net',
            'vat_amount',
        ]);
    }

    /** @test */
    public function it_validates_and_stores_vehicle_cost_with_correct_values()
    {
        // Tworzymy poprawny pojazd w bazie
        $vehicle = \App\Models\Vehicle::create([
            'brand' => 'TestBrand',
            'model' => 'TestModel',
            'vin' => '12345678901234567',
            'production_year' => 2023,
            'license_plate' => 'TEST123',
            'insurance_expiry' => now()->addYear()->toDateString(),
            'inspection_due' => now()->addYear()->toDateString(),
        ]);

        // Tworzymy poprawny typ kosztu
        $costType = \App\Models\CostType::create([
            'cost_type_name' => 'Fuel',
        ]);

        // Wysyłamy poprawne dane
        $response = $this->actingAs($this->admin)
            ->post('/vehicle-costs', [
                'vehicle_id' => $vehicle->vehicle_id,
                'date' => now()->toDateString(),
                'cost_type_id' => $costType->cost_type_id,
                'amount_gross' => 100.00,
                'vat_rate' => 23,
                'amount_net' => 81.30,
                'vat_amount' => 18.70,
                'description' => 'Fuel refill'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vehicle_costs', [
            'vehicle_id' => $vehicle->vehicle_id,
            'cost_type_id' => $costType->cost_type_id,
            'amount_gross' => 100.00,
            'vat_rate' => 23,
            'amount_net' => 81.30,
            'vat_amount' => 18.70,
            'description' => 'Fuel refill'
        ]);

        $vehicle->delete();
        $costType->delete();
    }

    /** @test */
    public function admin_can_create_valid_vehicle_cost()
    {
        $response = $this->actingAs($this->admin)
            ->post('/vehicle-costs', [
                'vehicle_id' => $this->vehicle->vehicle_id,
                'date' => now()->toDateString(),
                'cost_type_id' => $this->costType->cost_type_id,
                'description' => 'Wymiana opon',
                'amount_gross' => 200.50,
                'vat_rate' => 23,
                'amount_net' => 162.60,
                'vat_amount' => 37.90,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicle_costs', [
            'description' => 'Wymiana opon',
            'amount_gross' => 200.50,
        ]);
    }
}
