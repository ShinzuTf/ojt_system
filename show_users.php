<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = Illuminate\Support\Facades\DB::table('users')->get(['id', 'fname', 'lname', 'email', 'role']);

echo "=== OJT SYSTEM - TEST USERS ===\n\n";
echo str_pad("ID", 4) . " | " . str_pad("Name", 25) . " | " . str_pad("Email", 35) . " | " . str_pad("Role", 12) . "\n";
echo str_repeat("-", 80) . "\n";

foreach ($users as $user) {
    $name = $user->fname . " " . $user->lname;
    echo str_pad($user->id, 4) . " | " . str_pad($name, 25) . " | " . str_pad($user->email, 35) . " | " . str_pad($user->role, 12) . "\n";
}

echo "\n📝 Password for all users: 'password'\n";
echo "\n✅ Use any email above to login and test the system!\n";
?>
