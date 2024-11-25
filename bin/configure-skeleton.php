<?php

collect([
    'artisan',
    '.env.example',
    'database/.gitignore',
    'database/migrations/0001_01_01_000000_create_users_table.php',
    'database/migrations/0001_01_01_000001_create_cache_table.php',
    'database/migrations/0001_01_01_000002_create_jobs_table.php',
    'resources/views/*',
    'public/index.php',
    // 'tests/CreatesApplication.php',
])->transform(fn ($file) => "{$workingPath}/skeleton/{$file}")
    ->map(fn ($file) => str_contains($file, '*') ? [...$files->glob($file)] : $file)
    ->flatten()
    ->each(function ($file) use ($files, $workingPath) {
        $files->copy($file, "{$workingPath}/laravel".Illuminate\Support\Str::after($file, "{$workingPath}/skeleton"));
    });
$files->move("{$workingPath}/laravel/database/migrations/0001_01_01_000000_create_users_table.php", "{$workingPath}/laravel/migrations/0001_01_01_000000_testbench_create_users_table.php");
$files->move("{$workingPath}/laravel/database/migrations/0001_01_01_000001_create_cache_table.php", "{$workingPath}/laravel/migrations/0001_01_01_000001_testbench_create_cache_table.php");
$files->move("{$workingPath}/laravel/database/migrations/0001_01_01_000002_create_jobs_table.php", "{$workingPath}/laravel/migrations/0001_01_01_000002_testbench_create_jobs_table.php");

collect([
    // 'cache/0001_01_02_000000_testbench_create_cache_table' => 'Cache/Console/stubs/cache.stub',
    'notifications/0001_01_02_000000_testbench_create_notifications_table' => 'Notifications/Console/stubs/notifications.stub',
    // 'queue/0001_01_02_000000_testbench_create_jobs_table' => 'Queue/Console/stubs/jobs.stub',
    // 'queue/0001_01_02_000000_testbench_create_job_batches_table' => 'Queue/Console/stubs/batches.stub',
    // 'queue/0001_01_02_000000_testbench_create_failed_jobs_table' => 'Queue/Console/stubs/failed_jobs.stub',
    // 'session/0001_01_02_000000_testbench_create_sessions_table' => 'Session/Console/stubs/database.stub',
])->transform(fn ($file) => "{$workingPath}/vendor/laravel/framework/src/Illuminate/{$file}")
    ->each(function ($from, $to) use ($files, $workingPath) {
        $files->copy($from, "{$workingPath}/laravel/migrations/{$to}.php");
    })->keys()
    ->push(...[
        '0001_01_01_000000_testbench_create_users_table',
        '0001_01_01_000001_testbench_create_cache_table',
        '0001_01_01_000002_testbench_create_jobs_table',
    ])->each(function ($migration) use ($files, $workingPath) {
        $files->replaceInFile('class Create', 'class TestbenchCreate', "{$workingPath}/laravel/migrations/{$migration}.php");
    })->filter(fn ($migration) => str_starts_with($migration, 'queue'))
    ->mapWithKeys(fn ($migration) => match ($migration) {
        // 'queue/0001_01_02_000000_testbench_create_jobs_table' => [$migration => 'jobs'],
        // 'queue/0001_01_02_000000_testbench_create_job_batches_table' => [$migration => 'job_batches'],
        // 'queue/0001_01_02_000000_testbench_create_failed_jobs_table' => [$migration => 'failed_jobs'],
    })->each(function ($table, $migration) use ($files, $workingPath) {
        $files->replaceInFile(['{{tableClassName}}', '{{table}}'], [Illuminate\Support\Str::studly($table), $table], "{$workingPath}/laravel/migrations/{$migration}.php");
    });

transform([
    line('APP_KEY=', 0) => line('APP_KEY=AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', 0),
    line('DB_CONNECTION=mysql', 0) => line('DB_CONNECTION=sqlite', 0),
    line('DB_HOST=', 0) => line('# DB_HOST=', 0),
    line('DB_PORT=', 0) => line('# DB_PORT=', 0),
    line('DB_DATABASE=', 0) => line('# DB_DATABASE=', 0),
    line('DB_USERNAME=', 0) => line('# DB_USERNAME=', 0),
    line('DB_PASSWORD=', 0) => line('# DB_PASSWORD=', 0),
    line('SESSION_DRIVER=database', 0) => line('SESSION_DRIVER=cookie', 0),
    line('PHP_CLI_SERVER_WORKERS=', 0) => line('# PHP_CLI_SERVER_WORKERS=', 0),
], fn ($changes) => $files->replaceInFile(array_keys($changes), array_values($changes), "{$workingPath}/laravel/.env.example"));

collect([
    'config/*.php',
])->transform(fn ($file) => "{$workingPath}/vendor/laravel/framework/{$file}")
    ->map(fn ($file) => str_contains($file, '*') ? [...$files->glob($file)] : $file)
    ->flatten()
    ->each(function ($file) use ($files, $workingPath) {
        $files->copy($file, "{$workingPath}/laravel".Illuminate\Support\Str::after($file, "{$workingPath}/vendor/laravel/framework"));
    });

transform([
    line("'env' => env('APP_ENV', 'production'),", 1) => line("'env' => env('APP_ENV', 'workbench'),", 1),
], fn ($changes) => $files->replaceInFile(array_keys($changes), array_values($changes), "{$workingPath}/laravel/config/app.php"));

transform([
    line("'model' => env('AUTH_MODEL', App\Models\User::class),", 3) => line("'model' => env('AUTH_MODEL', Illuminate\Foundation\Auth\User::class),", 3),
], fn ($changes) => $files->replaceInFile(array_keys($changes), array_values($changes), "{$workingPath}/laravel/config/auth.php"));

transform([
    line("'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),", 1) => line("'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),", 1),
], fn ($changes) => $files->replaceInFile(array_keys($changes), array_values($changes), "{$workingPath}/laravel/config/hashing.php"));
