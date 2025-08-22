<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $stats = [
        'users' => DB::table('users')->count(),
        'projects' => DB::table('projects')->count(), 
        'tasks' => DB::table('tasks')->count(),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'database' => config('database.default'),
    ];

    return view('welcome', compact('stats'));
});

require __DIR__.'/auth.php';
