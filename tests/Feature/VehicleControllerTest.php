<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;

class VehicleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'adminTest@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_view_vehicle_list()
    {
        $this->actingAs($this->admin)
            ->get('/vehicles')
            ->assertStatus(200)
            ->assertInertia(function ($page) {
                $page->component('Vehicles/Vehicles')
                ->has('vehicles');
            });
    }

    /** @test */
    public function admin_can_add_a_vehicle()
    {
        $this->actingAs($this->admin)
            ->post('/vehicles', [
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'vin' => '1HGBH41JXMN109186',
                'production_year' => 2022,
                'license_plate' => 'ABC123',
                'insurance_expiry' => now()->addYear()->toDateString(),
                'inspection_due' => now()->addYear()->toDateString(),
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Pojazd został dodany.',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'license_plate' => 'ABC123',
        ]);
    }

    /** @test */
    public function admin_can_update_a_vehicle()
    {
        $vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '1HGBH41JXMN109186',
            'production_year' => 2022,
            'license_plate' => 'ABC123',
            'insurance_expiry' => now()->addYear()->toDateString(),
            'inspection_due' => now()->addYear()->toDateString(),
        ]);

        $this->actingAs($this->admin)
            ->put("/vehicles/{$vehicle->vehicle_id}", [
                'brand' => 'Updated Brand',
                'model' => 'Updated Model',
                'vin' => $vehicle->vin,
                'production_year' => $vehicle->production_year,
                'license_plate' => $vehicle->license_plate,
                'insurance_expiry' => $vehicle->insurance_expiry,
                'inspection_due' => $vehicle->inspection_due,
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Pojazd został zaktualizowany.',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'vehicle_id' => $vehicle->vehicle_id,
            'brand' => 'Updated Brand',
            'model' => 'Updated Model',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_vehicle()
    {
        $vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '1HGBH41JXMN109186',
            'production_year' => 2022,
            'license_plate' => 'ABC123',
            'insurance_expiry' => now()->addYear()->toDateString(),
            'inspection_due' => now()->addYear()->toDateString(),
        ]);

        $this->actingAs($this->admin)
            ->delete("/vehicles/{$vehicle->vehicle_id}")
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Pojazd został usunięty.',
            ]);

        $this->assertDatabaseMissing('vehicles', [
            'vehicle_id' => $vehicle->vehicle_id,
        ]);
    }
}
