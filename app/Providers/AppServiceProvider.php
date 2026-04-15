<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force URL root and scheme from the current request so generated
        // URLs and redirects use the incoming host (useful for ngrok/local)
        if (! $this->app->runningInConsole()) {
            $request = request();
            try {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
                if ($request->isSecure()) {
                    URL::forceScheme('https');
                }
            } catch (\Throwable $e) {
                // ignore when request is not available
            }
        }
    }
}
