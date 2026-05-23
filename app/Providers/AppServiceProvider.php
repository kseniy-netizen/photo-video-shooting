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
        // Если мы на Vercel, применяем его настройки, если нет — просто идем дальше
        if (getenv('VERCEL')) {
            $this->applyVercelDefaults();
        }
    }

    private function applyVercelDefaults(): void
    {
        putenv('CACHE_DRIVER=array');
        $_ENV['CACHE_DRIVER'] = 'array';

        putenv('SESSION_DRIVER=cookie');
        $_ENV['SESSION_DRIVER'] = 'cookie';

        putenv('LOG_CHANNEL=stderr');
        $_ENV['LOG_CHANNEL'] = 'stderr';

        $viewsPath = '/tmp/framework/views';
        putenv('VIEW_COMPILED_PATH=' . $viewsPath);
        $_ENV['VIEW_COMPILED_PATH'] = $viewsPath;
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // На хостинге REG.RU принудительно включаем HTTPS для ссылок, если сайт работает по SSL
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Если это Vercel, подтягиваем его URL
        if (getenv('VERCEL')) {
            $appUrl = rtrim((string) env('APP_URL', ''), '/');
            if ($appUrl !== '') {
                URL::forceRootUrl($appUrl);
            }
        }
    }
}
