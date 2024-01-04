<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AppointmentTypesController;
use App\Models\AppointmentType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AppointmentTypesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testIndex()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $appointmentType1 = factory(AppointmentType::class)->create(['store_id' => $user->store_id]);
        $appointmentType2 = factory(AppointmentType::class)->create(['store_id' => $user->store_id]);
        $response = $this->get('/appointment-types');
        $response->assertStatus(200);
        $response->assertJson([$appointmentType1->toArray(), $appointmentType2->toArray()]);
    }

    public function testStore()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $appointmentTypeData = [
            'title' => 'New Appointment Type',
            'slug' => 'new-appointment-type',
            'length' => 30,
        ];

        $response = $this->post('/appointment-types', $appointmentTypeData);

        $response->assertStatus(201);

        $response->assertJson($appointmentTypeData);
    }

    public function testShow()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $appointmentType = factory(AppointmentType::class)->create(['store_id' => $user->store_id]);
        $response = $this->get('/appointment-types/' . $appointmentType->id);
        $response->assertStatus(200);
        $response->assertJson($appointmentType->toArray());
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $appointmentType = factory(AppointmentType::class)->create(['store_id' => $user->store_id]);
        $updatedData = [
            'title' => 'Updated Title',
            'length' => 60,
        ];
        $response = $this->put('/appointment-types/' . $appointmentType->id, $updatedData);
        $response->assertStatus(201);
        $response->assertJson($updatedData);
    }

    public function testDestroy()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $appointmentType = factory(AppointmentType::class)->create(['store_id' => $user->store_id]);
        $response = $this->delete('/appointment-types/' . $appointmentType->id);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Appointment type deleted successfully']);
    }

} 
