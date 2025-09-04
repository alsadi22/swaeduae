<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Support\Integrations;

class SocialAuthController extends Controller
{
  public function redirect(string $provider){
    abort_unless(Integrations::enabled($provider), 404);
    Integrations::primeSocialite($provider);
    return Socialite::driver($provider)->redirect();
  }
  public function callback(string $provider){
    Integrations::primeSocialite($provider);
    $s = Socialite::driver($provider)->stateless()->user();
    $email = $s->getEmail() ?: ($provider.'-'.Str::uuid().'@example.invalid');
    $user = User::updateOrCreate(
      ['email'=>$email],
      ['name'=>$s->getName() ?: Str::before($email,'@'), 'email_verified_at'=>now(), 'password'=>bcrypt(Str::random(40))]
    );
    Auth::login($user, true);
    return redirect()->intended('/admin');
  }
}
