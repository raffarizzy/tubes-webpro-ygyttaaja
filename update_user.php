<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::find(1);
if ($u) {
    $u->email = 'usera@medcom.com';
    $u->password = \Illuminate\Support\Facades\Hash::make('Password123!');
    $u->save();
    echo "User 1 updated to usera@medcom.com / Password123!\n";
} else {
    echo "User 1 not found\n";
}
