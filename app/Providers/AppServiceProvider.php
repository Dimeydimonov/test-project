<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use App\Services\Implementations\Auth\AuthService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Interfaces\ArtworkServiceInterface::class,
            \App\Services\Implementations\ArtworkService::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );

        Config::set('database.connections.mysql.host', 'mysql');
        Config::set('database.connections.mysql.database', 'test');
        Config::set('database.connections.mysql.username', 'test');
        Config::set('database.connections.mysql.password', 'test');
        
        $tmpBase = sys_get_temp_dir() . '/katrin_gallery_storage';
        
        if (!file_exists($tmpBase . '/framework/cache/data')) {
            mkdir($tmpBase . '/framework/cache/data', 0755, true);
        }
        if (!file_exists($tmpBase . '/framework/sessions')) {
            mkdir($tmpBase . '/framework/sessions', 0755, true);
        }
        if (!file_exists($tmpBase . '/logs')) {
            mkdir($tmpBase . '/logs', 0755, true);
        }
        
        Config::set('cache.default', 'file');
        Config::set('cache.stores.file.path', $tmpBase . '/framework/cache/data');
        Config::set('view.compiled', $tmpBase . '/framework/views');
        Config::set('session.driver', 'file');
        Config::set('session.files', $tmpBase . '/framework/sessions');
        Config::set('logging.channels.single.path', $tmpBase . '/logs/laravel.log');
        Config::set('logging.channels.daily.path', $tmpBase . '/logs/laravel.log');
    }

    public function boot(): void
    {
        $tmpBase = sys_get_temp_dir() . '/katrin_gallery_storage';
        $paths = [
            $tmpBase,
            $tmpBase . '/app',
            $tmpBase . '/app/public',
            $tmpBase . '/framework',
            $tmpBase . '/framework/cache',
            $tmpBase . '/framework/cache/data',
            $tmpBase . '/framework/sessions',
            $tmpBase . '/framework/views',
            $tmpBase . '/logs',
        ];

        foreach ($paths as $p) {
            if (!is_dir($p)) {
                @mkdir($p, 0775, true);
            }
        }

        $this->app->useStoragePath($tmpBase);
    }
}
