<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$m = \App\Models\User::where('email', 'hector@oberstaff.com')->first();
$p = \App\Models\User::where('email', 'arquidev@gmail.com')->first();

echo "Manager: " . ($m ? "ID: {$m->id}, EmpID: {$m->empleador_id}, Type: {$m->tipo_usuario}, Manager: {$m->is_manager}" : "Not Found") . PHP_EOL;
echo "Pro: " . ($p ? "ID: {$p->id}, EmpID: {$p->empleador_id}, Type: {$p->tipo_usuario}" : "Not Found") . PHP_EOL;

if ($m) {
    echo "Manager Colleagues (count: " . $m->compañerosDeTrabajo()->count() . "):" . PHP_EOL;
    foreach ($m->compañerosDeTrabajo() as $colleague) {
        echo "- {$colleague->name} ({$colleague->email}) [Type: {$colleague->tipo_usuario}, EmpID: {$colleague->empleador_id}]" . PHP_EOL;
    }
}
