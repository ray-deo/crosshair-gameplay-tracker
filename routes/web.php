
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameProgressController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ScreenshotController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (NO LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | LIBRARY (MAIN USER DASHBOARD)
    |--------------------------------------------------------------------------
    */
    Route::get('/library', [LibraryController::class, 'index'])
        ->name('library');

    Route::post('/library/add', [LibraryController::class, 'add'])
        ->name('library.add')
        ->middleware('auth');

    Route::delete('/library/remove/{id}', [LibraryController::class, 'destroy'])
        ->name('library.remove');

    Route::post('/library/import-steam', [LibraryController::class, 'importFromSteam'])
        ->name('library.import-steam');

    Route::get('/dashboard', function () {
    return view('landing');
})->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | SEARCH (RAWG)
    |--------------------------------------------------------------------------
    */
    Route::get('/search', [SearchController::class, 'index'])
        ->name('search');

    /*
    |--------------------------------------------------------------------------
    | GAME DETAIL PAGE
    |--------------------------------------------------------------------------
    */
    
    Route::get('/game/{id}', [GameController::class, 'show'])
    ->name('game.show')
    ->middleware('auth');

    Route::post('/game/{id}/notes', [NoteController::class, 'store'])
        ->name('notes.store');
    Route::put('/notes/{id}', [NoteController::class, 'update'])
        ->name('notes.update');
    Route::delete('/notes/{id}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

    Route::post('/game/{id}/screenshots', [ScreenshotController::class, 'upload'])
        ->name('screenshots.upload');
    Route::delete('/screenshots/{id}', [ScreenshotController::class, 'destroy'])
        ->name('screenshots.destroy');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Admin Panel';
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/library', function () {
        return 'User Library';
    });
});
Route::get('/library', [LibraryController::class, 'index']);
});