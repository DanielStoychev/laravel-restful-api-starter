<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    // Get database statistics
    $stats = [
        'users' => DB::table('users')->count(),
        'projects' => DB::table('projects')->count(),
        'tasks' => DB::table('tasks')->count(),
        'laravel_version' => app()->version()
    ];
    
    return view('welcome', $stats);
});

require __DIR__.'/auth.php';
