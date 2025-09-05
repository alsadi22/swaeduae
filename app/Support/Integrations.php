<?php
namespace App\Support;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Integrations {
  protected static function val(string $key,$default=null){
    try{ if(!Schema::hasTable('settings')) return $default; return Setting::getValue($key,$default); }
    catch(\Throwable $e){ return $default; }
  }
  public static function enabled(string $p): bool{
    $db = static::val("oauth.$p.enabled");
    if(!is_null($db)) return filter_var($db, FILTER_VALIDATE_BOOL);
    return filter_var(env('OAUTH_'.strtoupper($p).'_ENABLED', false), FILTER_VALIDATE_BOOL);
  }
  public static function id(string $p){ return static::val("oauth.$p.client_id",     env(strtoupper($p).'_CLIENT_ID')); }
  public static function secret(string $p){ return static::val("oauth.$p.client_secret", env(strtoupper($p).'_CLIENT_SECRET')); }
  public static function redirect(string $p){ return static::val("oauth.$p.redirect",  config("services.$p.redirect")); }

  public static function primeSocialite(string $p): void{
    config([
      "services.$p.client_id"     => static::id($p),
      "services.$p.client_secret" => static::secret($p),
      "services.$p.redirect"      => static::redirect($p),
    ]);
  }
  // Stripe
  public static function stripeEnabled(): bool{
    $db = static::val("payments.stripe.enabled");
    if(!is_null($db)) return filter_var($db, FILTER_VALIDATE_BOOL);
    return filter_var(env('STRIPE_ENABLED', false), FILTER_VALIDATE_BOOL);
  }
  public static function stripeKey(){ return static::val("payments.stripe.key", env('STRIPE_KEY')); }
  public static function stripeSecret(){ return static::val("payments.stripe.secret", env('STRIPE_SECRET')); }
  public static function primeStripe(): void{
    config(["services.stripe.key"=>static::stripeKey(),"services.stripe.secret"=>static::stripeSecret()]);
  }
}
