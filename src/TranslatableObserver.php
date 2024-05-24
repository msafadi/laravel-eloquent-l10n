<?php

namespace Safadi\Eloquent\L10n;

use Illuminate\Database\Eloquent\Model;

class TranslatableObserver
{

    /**
     * Handle the Model "created" event.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function created(Model $model): void
    {
        $this->handle($model);
    }

    /**
     * Handle the Model "updated" event.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function updated(Model $model): void
    {
        $this->handle($model);
    }

    /**
     * Handle the Model "saved" event.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function Saved(Model $model): void
    {
        $this->handle($model);
    }

    /**
     * Handle the Model "deleted" event.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function deleted(Model $model): void
    {
        $model->translations()->delete();
    }

    /**
     * Handle the event.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function handle(Model $model): void
    {
        if ($translations = $model->getTranslationsForSave()) {                
            foreach ($translations as $locale => $attributes) {
                $model->translate($attributes, $locale);
            }
            $model->clearTranslationsForSave();
        }
    }
}