<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Hospital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected $hospital;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->hospital = Hospital::create([
            'name' => 'Test Hospital',
            'slug' => 'test-hospital',
            'is_active' => true
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);
    }

    public function test_users_are_filtered_by_technical_pole()
    {
        // User with technical role but NO service
        User::create([
            'name' => 'Lab Tech',
            'email' => 'tech@test.com',
            'role' => 'lab_technician',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        // User with medical role in technical service
        $techService = Service::create([
            'name' => 'Laboratory',
            'type' => 'technical',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);
        
        User::create([
            'name' => 'Lab Doctor',
            'email' => 'labdoc@test.com',
            'role' => 'doctor_lab',
            'service_id' => $techService->id,
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        // Random medical user (should NOT be in technical)
        User::create([
            'name' => 'Standard Doctor',
            'email' => 'doc@test.com',
            'role' => 'doctor',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)->get('/users?pole=technical');

        $response->assertStatus(200);
        $response->assertSee('Lab Tech');
        $response->assertSee('Lab Doctor');
        $response->assertDontSee('Standard Doctor');
    }

    public function test_users_are_filtered_by_support_pole()
    {
        // Cashier with NO service
        User::create([
            'name' => 'Cashier One',
            'email' => 'cashier@test.com',
            'role' => 'cashier',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        // Admin (support)
        User::create([
            'name' => 'Staff Admin',
            'email' => 'staff@test.com',
            'role' => 'administrative',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        // Doctor (not support)
        User::create([
            'name' => 'Some Doctor',
            'email' => 'somedoc@test.com',
            'role' => 'doctor',
            'hospital_id' => $this->hospital->id,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)->get('/users?pole=support');

        $response->assertStatus(200);
        $response->assertSee('Cashier One');
        $response->assertSee('Staff Admin');
        $response->assertDontSee('Some Doctor');
    }
}
