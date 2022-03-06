<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    /**
     * Redirect to provider
    */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
          
    /**
     * Handle provider callback
    */
    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
         
            $providerUser = User::where('provider_id', $user->id)->first();
        
            if($providerUser){
                Auth::login($providerUser);
            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider_id'=> $user->id,
                    'password' => Hash::make('12345678')
                ]);
        
                Auth::login($newUser);
            }
            return redirect('dashboard');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
