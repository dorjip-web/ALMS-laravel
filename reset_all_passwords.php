<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hash = '$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu';

$updated = \Illuminate\Support\Facades\DB::table('tab1')->update(['password' => $hash]);

echo "✓ Updated $updated user password(s) to bcrypt hash of NTMH_123\n";
