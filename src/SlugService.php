<?php 
namespace Harran\Slugit;
use Harran\Slugit\ModelObserver;

trait SlugService{

    public static function bootSlugService()
    {
    	static::observe(app(ModelObserver::class));
    }

}