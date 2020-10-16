<?php


namespace Fengers\TikTok;


use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class TikTokServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/config/tiktok.php' => config_path('tiktok.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('tiktok');
        }
    }

    public function register()
    {
        $this->app->singleton(TikTokManager::class,function ($app){
            return new TikTokManager($app->make('config')->get('tiktok'));
        });

        $this->mergeConfigFrom(
            __DIR__.'/config/tiktok.php', 'tiktok'
        );
    }

    public function provides()
    {
        return [TikTokManager::class];
    }
}
