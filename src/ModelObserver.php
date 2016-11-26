<?php

namespace Harran\Slugit;
use \Harran\Slugit\Services\SlugService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

class ModelObserver
{
    protected $slugService;
    protected $events;

    public function __construct(SlugService $slugService){
        $this->slugService = $slugService;
    }

    /**
     * Listen to the model saving event.
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function saving(Model $model)
    {
        $this->slugService->generate( $model, $model->slugSettings() );
        return true; 
    }
}