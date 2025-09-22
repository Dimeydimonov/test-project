<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\Artwork\ArtworkServiceInterface;
use App\Services\Implementations\Artwork\ArtworkService;

class ArtworkServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ArtworkServiceInterface::class,
            ArtworkService::class
        );
    }

    /**
     * @return void
     */
    public function boot()
    {
        //
    }
}
