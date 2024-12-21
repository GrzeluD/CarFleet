<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMileage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleMileageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $driver;
    protected $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'adminTest@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->driver = User::create([
            'name' => 'Driver User',
            'email' => 'driver@example.com',
            'password' => bcrypt('password'),
            'role' => 'driver'
        ]);

        $this->vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '12345678901234567',
            'production_year' => 2023,
            'license_plate' => 'XYZ123',
            'insurance_expiry' => now()->addYear()->toDateString(),
            'inspection_due' => now()->addYear()->toDateString(),
        ]);
    }

    /** @test */
    public function admin_can_view_mileage_list()
    {
        $this->actingAs($this->admin)
            ->get('/vehicle-mileages')
            ->assertStatus(200)
            ->assertInertia(function ($page) {
                $page->component('VehicleMileages/VehicleMileages')
                    ->has('vehicleMileages');
            });
    }

    /** @test */
    public function driver_can_add_vehicle_mileage()
    {
        $response = $this->actingAs($this->driver)
            ->post('/vehicle-mileages', [
                'vehicle_id' => $this->vehicle->vehicle_id,
                'user_id' => $this->driver->id,
                'date' => now()->toDateString(),
                'start_mileage' => 1000,
                'end_mileage' => 1100,
                'location_start' => 'Start Point',
                'location_end' => 'End Point',
                'route_description' => 'Daily commute',
                'distance_traveled' => 100,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vehicle_mileages', [
            'vehicle_id' => $this->vehicle->vehicle_id,
            'user_id' => $this->driver->id,
            'start_mileage' => 1000,
            'end_mileage' => 1100,
            'location_start' => 'Start Point',
            'location_end' => 'End Point',
            'route_description' => 'Daily commute',
            'distance_traveled' => 100,
        ]);
    }

    /** @test */
    public function it_validates_correct_vehicle_mileage_input()
    {
        $response = $this->actingAs($this->driver)
            ->post('/vehicle-mileages', [
                'vehicle_id' => $this->vehicle->vehicle_id,
                'user_id' => $this->driver->id,
                'date' => now()->toDateString(),
                'start_mileage' => 1000,
                'end_mileage' => 1100,
                'location_start' => 'Start Location',
                'location_end' => 'End Location',
                'route_description' => 'Daily commute route',
                'distance_traveled' => 100,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vehicle_mileages', [
            'vehicle_id' => $this->vehicle->vehicle_id,
            'user_id' => $this->driver->id,
            'start_mileage' => 1000,
            'end_mileage' => 1100,
            'location_start' => 'Start Location',
            'location_end' => 'End Location',
            'route_description' => 'Daily commute route',
            'distance_traveled' => 100,
        ]);
    }

    /** @test */
    public function admin_can_update_vehicle_mileage()
    {
        $mileage = VehicleMileage::create([
            'vehicle_id' => $this->vehicle->vehicle_id,
            'user_id' => $this->driver->id,
            'date' => now()->toDateString(),
            'start_mileage' => 1000,
            'end_mileage' => 1100,
            'location_start' => 'Start Point',
            'location_end' => 'End Point',
            'route_description' => 'Daily commute',
            'distance_traveled' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->put("/vehicle-mileages/{$mileage->mileage_id}", [
                'vehicle_id' => $this->vehicle->vehicle_id,
                'user_id' => $this->driver->id,
                'date' => now()->toDateString(),
                'start_mileage' => 1100,
                'end_mileage' => 1200,
                'location_start' => 'Updated Start',
                'location_end' => 'Updated End',
                'route_description' => 'Updated Route',
                'distance_traveled' => 200,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('vehicle_mileages', [
            'mileage_id' => $mileage->mileage_id,
            'vehicle_id' => $this->vehicle->vehicle_id,
            'user_id' => $this->driver->id,
            'date' => now()->toDateString(),
            'start_mileage' => 1100,
            'end_mileage' => 1200,
            'location_start' => 'Updated Start',
            'location_end' => 'Updated End',
            'route_description' => 'Updated Route',
            'distance_traveled' => 200,
        ]);
    }

    /** @test */
    public function admin_can_delete_vehicle_mileage()
    {
        $mileage = VehicleMileage::create([
            'vehicle_id' => $this->vehicle->vehicle_id,
            'user_id' => $this->driver->id,
            'date' => now()->toDateString(),
            'start_mileage' => 1000,
            'end_mileage' => 1100,
            'location_start' => 'Start Point',
            'location_end' => 'End Point',
            'route_description' => 'Daily commute',
            'distance_traveled' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/vehicle-mileages/{$mileage->mileage_id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('vehicle_mileages', [
            'mileage_id' => $mileage->mileage_id,
        ]);
    }
}
