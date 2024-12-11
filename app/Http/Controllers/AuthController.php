<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $files = Storage::files('media/wallpaper');
        $jpgFiles = array_filter($files, function ($file) {
            return str_ends_with($file, '.jpg');
        });

        return view('auth.login', [
            'nb_wall' => count($jpgFiles),
            'error' => $request->has('error') ? $request->get('error') : null,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        \Session::flush();

        return redirect()->route('login');
    }

    public function redirect(string $provider)
    {
        return Socialite::driver($provider)
            ->scopes(['identify', 'email', 'connections', 'guilds', 'guilds.members.read'])
            ->redirect();
    }

    public function callback(string $provider)
    {
        if(request()->has('error')) {
            return redirect()->route('login', ['error' => "Accès Interdit"]);
        }

        $userSocialite = Socialite::driver($provider)->user();

        $user = User::firstOrCreate([
                'discord_id' => $userSocialite->id,
            ], [
                "name" => $userSocialite->name,
                "email" => $userSocialite->email,
                "avatar" => $userSocialite->avatar,
                "password" => $userSocialite->token,
                'discord_access_token' => $userSocialite->token,
                'discord_refresh_token' => $userSocialite->refreshToken,
            ]);

        $sanctumToken = $user->createToken('botAccessToken')->plainTextToken;
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
