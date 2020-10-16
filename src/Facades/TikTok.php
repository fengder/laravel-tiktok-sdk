<?php


namespace Fengers\TikTok\Facades;


use Fengers\TikTok\TikTokManager;
use Illuminate\Support\Facades\Facade;

class TikTok extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TikTokManager::class;
    }
}
