<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\Console\Output\ConsoleOutput;

class OAuthController extends Controller
{
    public function yandex()
    {
        return Socialite::driver('yandex')->stateless()->redirect();
    }

    public function yandexRedirect()
    {
        $user = Socialite::driver('yandex')->stateless()->user();
        dd($user);
    }
}
