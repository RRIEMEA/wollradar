<?php

use App\Models\Brand;
use App\Models\Color;
use App\Models\Location;
use App\Models\Material;
use App\Models\Project;
use App\Models\User;
use App\Models\Yarn;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\YarnController;
use App\Http\Controllers\Admin\UserApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/impressum', 'legal.imprint')->name('legal.imprint');
Route::view('/datenschutz', 'legal.privacy')->name('legal.privacy');

Route::get('/dashboard', function () {
    $userId = auth()->id();

    return view('dashboard', [
        'stats' => [
            [
                'label' => 'Yarns',
                'value' => Yarn::query()->where('user_id', $userId)->count(),
                'hint' => 'Knäuel und Restbestände',
                'route' => route('yarns.index'),
            ],
            [
                'label' => 'Projects',
                'value' => Project::query()->where('user_id', $userId)->count(),
                'hint' => 'Aktive und geplante Arbeiten',
                'route' => route('projects.index'),
            ],
            [
                'label' => 'Colors',
                'value' => Color::query()->where('user_id', $userId)->count(),
                'hint' => 'Farben für schnelle Zuordnung',
                'route' => route('colors.index'),
            ],
            [
                'label' => 'Materials',
                'value' => Material::query()->where('user_id', $userId)->count(),
                'hint' => 'Merino, Baumwolle und mehr',
                'route' => route('materials.index'),
            ],
            [
                'label' => 'Brands',
                'value' => Brand::query()->where('user_id', $userId)->count(),
                'hint' => 'Hersteller und Serien',
                'route' => route('brands.index'),
            ],
            [
                'label' => 'Locations',
                'value' => Location::query()->where('user_id', $userId)->count(),
                'hint' => 'Schrank, Box oder Regal',
                'route' => route('locations.index'),
            ],
        ],
        'pendingApprovals' => auth()->user()->is_admin
            ? User::query()->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'PENDING');
            })->count()
            : 0,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users/pending', [UserApprovalController::class, 'pending'])
            ->name('users.pending');

        Route::patch('/users/{user}/approve', [UserApprovalController::class, 'approve'])
            ->name('users.approve');

        Route::patch('/users/{user}/reject',  [UserApprovalController::class, 'reject'])
            ->name('users.reject');

        Route::patch('/users/{user}/deactivate', [UserApprovalController::class, 'deactivate'])
            ->name('users.deactivate');

        Route::delete('/users/{user}', [UserApprovalController::class, 'destroy'])
            ->name('users.destroy');

        Route::post('/users/{user}/make-admin', [UserApprovalController::class, 'makeAdmin'])
            ->name('users.makeAdmin');

        Route::post('/users/{user}/remove-admin', [UserApprovalController::class, 'removeAdmin'])
            ->name('users.removeAdmin');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    // Meta lists
    Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
    Route::post('/colors', [ColorController::class, 'store'])->name('colors.store');
    Route::delete('/colors/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // Projects (Controller hat create/edit/update -> Routes ergänzen)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Yarns
    Route::patch('/yarns/{yarn}/quantity', [YarnController::class, 'adjustQuantity'])->name('yarns.quantity.adjust');
    Route::patch('/yarns/{yarn}/finish-project', [YarnController::class, 'finishProject'])->name('yarns.finish-project');
    Route::resource('yarns', YarnController::class)->except('show');
});

require __DIR__ . '/auth.php';
