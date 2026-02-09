<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleLabelTest extends TestCase
{
    public function test_doctor_lab_role_label()
    {
        $user = new User(['role' => 'doctor_lab']);
        $this->assertEquals('Biologiste', $user->getRoleLabel(), "Role 'doctor_lab' should be labeled 'Biologiste', but got '{$user->getRoleLabel()}'");
    }

    public function test_doctor_radio_role_label()
    {
        $user = new User(['role' => 'doctor_radio']);
        $this->assertEquals('Radiologue', $user->getRoleLabel(), "Role 'doctor_radio' should be labeled 'Radiologue', but got '{$user->getRoleLabel()}'");
    }
}
