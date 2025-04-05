<?php
// routes/api.php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'signIn']);
Route::post('/log-unauthenticated-access', [AuthController::class, 'logUnauthenticatedAccess']);
