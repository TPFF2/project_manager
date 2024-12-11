<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/bot/token', function (Request $request) {
    $user = Auth::user();

    if (!$user || !$user->discord_id) {
        return response()->json(['error' => 'Utilisateur non lié à Discord.'], 403);
    }

    // Générer un token spécifique pour le bot (si besoin d'un token unique)
    $botToken = $user->createToken('BotAccessToken')->plainTextToken;

    return response()->json([
        'token' => $botToken,
    ]);
});

Route::get('/discord/user', function (Request $request) {
    $user = Auth::user();

    // Vérifiez que l'utilisateur est lié à Discord
    if (!$user || !$user->discord_id) {
        return response()->json(['error' => 'Utilisateur non lié à Discord.'], 403);
    }

    return response()->json([
        'discord_id' => $user->discord_id,
    ]);
});

Route::post('/discord/status', function (Request $request) {
    $data = $request->validate([
        'user_id' => "required|string",
        'status' => "required|string",
    ]);

    \App\Models\UserStatus::updateOrCreate([
        'user_id' => $data['user_id'],
        ['status' => $data['status'], 'updated_at' => \Carbon\Carbon::now()],
    ]);

    return response()->json(['message' => 'Statut reçu avec succès']);
});
