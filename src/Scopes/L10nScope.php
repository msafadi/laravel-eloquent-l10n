<?php

namespace Safadi\Eloquent\L10n\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class L10nScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $columns = array_merge($builder->getQuery()->columns ?? ['*'], $model->getL10nAttributes());
        $builder->select($columns);
        $builder->leftJoin(
            $model->getL10nTable(), function ($join) use ($model) {
                $join->on($model->getL10nForigenKeyName(), '=', $model->getKeyName())
                     ->where('locale', '=', $model->getLocale());
            }
        );
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        $this->addWithoutTranslations($builder);
        $this->addwithTranslations($builder);
    }

    /**
     * Add the without-translations extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutTranslations(Builder $builder)
    {
        $builder->macro('withoutTranslations', function (Builder $builder) {
            $builder->withoutGlobalScope($this);

            return $builder;
        });
    }

    /**
     * Add the without-translations extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addwithTranslations(Builder $builder)
    {
        $builder->macro('withTranslations', function (Builder $builder, $attributes) {
            $builder->getModel()->setTranslationsForSave($attributes);

            return $builder;
        });
    }

}