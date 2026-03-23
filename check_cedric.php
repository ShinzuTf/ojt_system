<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('fname', 'LIKE', '%Cedric%')->orWhere('lname', 'LIKE', '%Ebidag%')->with('ojtInfo')->first();
if($u) {
    echo json_encode([
        'id' => $u->id,
        'fname' => $u->fname,
        'lname' => $u->lname,
        'email' => $u->email,
        'has_ojt' => !!$u->ojtInfo,
        'company' => $u->ojtInfo?->company_name,
        'supervisor' => $u->ojtInfo?->supervisor_name
    ], JSON_PRETTY_PRINT);
} else {
    echo "User not found";
}
