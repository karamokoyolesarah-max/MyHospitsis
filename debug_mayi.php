<?php
use App\Models\MedecinExterne;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'mayi@gmail.com';
$m = MedecinExterne::where('email', $email)->first();
if ($m) {
    echo "MedecinExterne found. Status: " . $m->statut . "\n";
    $m->password = Hash::make('password');
    $m->save();
    echo "Password reset to 'password'\n";
} else {
    echo "MedecinExterne NOT found.\n";
}

$u = User::where('email', $email)->first();
if ($u) {
    echo "User found in users table! This might be a conflict. Role: " . $u->role . "\n";
} else {
    echo "No conflict in users table.\n";
}
