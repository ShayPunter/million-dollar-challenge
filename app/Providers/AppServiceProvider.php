<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::define('viewAdmin', fn (User $user) => $user->isAdmin());

        $this->configureAccountLimits();
    }

    /**
     * Card 5532: sign-ups and "forgot password" requests per IP address.
     * Going over shows the form's email error instead of a bare 429 page.
     */
    private function configureAccountLimits(): void
    {
        $tooMany = function (Request $request, array $headers) {
            $minutes = max(1, (int) ceil(((int) ($headers['Retry-After'] ?? 60)) / 60));

            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors([
                    'email' => trans_choice(
                        'Too many attempts. Please try again in :count minute.|Too many attempts. Please try again in :count minutes.',
                        $minutes,
                    ),
                ]);
        };

        RateLimiter::for('register', fn (Request $request) => Limit::perHour(
            (int) (config('auth.account_limits.register_per_hour') ?: 5),
        )->by('register|'.$request->ip())->response($tooMany));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(
            (int) (config('auth.account_limits.password_resets_per_minute') ?: 6),
        )->by('password-reset|'.$request->ip())->response($tooMany));
    }
}
