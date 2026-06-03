<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$compiler = app(\Illuminate\View\Compilers\BladeCompiler::class);

$views = [
    'dashboard_admin/ppdb.blade.php',
    'format_file/pdf.blade.php',
    'dashboard_admin/murid.blade.php',
];

foreach ($views as $rel) {
    $path = resource_path("views/$rel");
    try {
        $compiler->compileString(file_get_contents($path));
        echo "OK: $rel\n";
    } catch (\Exception $e) {
        echo "SYNTAX ERROR: $rel\n  => " . $e->getMessage() . "\n";
    }
}
