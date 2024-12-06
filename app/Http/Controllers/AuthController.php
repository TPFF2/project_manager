<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function login()
    {
        $files = Storage::files('media/wallpaper');
        $jpgFiles = array_filter($files, function ($file) {
            return str_ends_with($file, '.jpg');
        });

        return view('auth.login', [
            'nb_wall' => count($jpgFiles)
        ]);
    }
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $userSocialite = Socialite::driver($provider)->user();

        $user = User::firstOrCreate([
                'discord_id' => $userSocialite->id,
            ], [
                "name" => $userSocialite->name,
                "email" => $userSocialite->email,
                "avatar" => $userSocialite->avatar,
                "password" => $userSocialite->token,
            ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
