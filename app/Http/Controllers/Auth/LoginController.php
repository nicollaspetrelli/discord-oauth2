<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function discord()
    {
        return Socialite::driver('discord')->scopes([
            'identify',
            'email',
            'connections',
            'guilds',
            // 'guilds.join',
            // 'guilds.members.read',
            // 'gdm.join',
            // 'rpc',
            // 'rpc.notifications.read',
            // 'rpc.voice.read',
            // 'rpc.voice.write',
            // 'rpc.activities.write',
            // 'activities.read',
            // 'activities.write',
            // 'relationships.read',
        ])->redirect();
    }

    public function discordCallback()
    {
        $discordUser = Socialite::driver('discord')->stateless()->user();

        $user = User::where('discord_id', $discordUser->id)->first();

        if ($user) {
            $user->update([
                'discord_token' => $discordUser->token,
                'discord_refresh_token' => $discordUser->refreshToken,
            ]);
        } else {
            $user = User::create([
                'name' => $discordUser->name,
                'email' => $discordUser->email,
                'password' => Hash::make(random_bytes(16)),
                'discord_id' => $discordUser->id,
                'discord_nickname' => $discordUser->nickname,
                'discord_avatar' => $this->getDiscordAvatar($discordUser->id, $discordUser->user['avatar']),
                'discord_token' => $discordUser->token,
                'discord_refresh_token' => $discordUser->refreshToken,
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    private function getDiscordAvatar(string $userId, string $pictureId): string
    {
        return 'https://cdn.discordapp.com/avatars/' . $userId . '/' . $pictureId . '.png?size=512';
    }
}
