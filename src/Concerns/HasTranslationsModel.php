<?php

namespace Safadi\Eloquent\L10n\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Safadi\Eloquent\L10n\Scopes\L10nScope;
use Illuminate\Support\Str;
use Safadi\Eloquent\L10n\Contracts\L10nModel;
use Safadi\Eloquent\L10n\L10n;
use Safadi\Eloquent\L10n\TranslatableObserver;

trait HasTranslationsModel
{
    use HasLocale;
    
    /**
     * Model translations for saving
     * [
     *   'en' => [
     *     'attribute' => 'value',
     *     ...
     *   ],
     *   'ar' => [
     *     'attribute' => 'value',
     *     ...
     *   ],
     *   ...,
     * ]
     * 
     * @var array
     */
    protected $withTranslations;

    /**
     * Boot trait
     * @return void
     */
    protected static function bootHasTranslationsModel()
    {
        static::addGlobalScope(new L10nScope);

        static::observe(TranslatableObserver::class);
    }

    /**
     * Translations relationship
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(config('eloquent-l10n.model', L10n::class), $this->getL10nForigenKeyName());
    }

    /**
     * Get the translations model
     * 
     * @return \Safadi\Eloquent\L10n\Contracts\L10nModel
     */
    public function getL10nModel(): L10nModel
    {
        $model = config('eloquent-l10n.model', L10n::class);
        $instance = new $model;
        $instance->setTable($this->getL10nTable());
        return $instance;
    }

    /**
     * Get the translations table for the model
     * 
     * @return string
     */
    public function getL10nTable()
    {
        return $this->getTable() . config('eloquent-l10n.table_postfix', '_l10n');
    }

    /**
     * Get the translatable attributes for the model
     * 
     * @return array
     */
    public function getL10nAttributes()
    {
        return [];
    }

    /**
     * Get the model forigen key in translations table
     * 
     * @return string
     */
    public function getL10nForigenKeyName()
    {
        return Str::singular($this->getTable()).'_'.$this->getKeyName();
    }

    /**
     * Get the name of the locale column in translations table
     * 
     * @return string
     */
    public function getLocaleColumnName()
    {
        return config('eloquent-l10n.locale_key', 'locale');
    }

    /**
     * Save model translations
     * 
     * @param array $attributes
     * @param string $locale optional
     * 
     * @return $this
     */
    public function translate($attributes, $locale = null)
    {
        tap($this->getLocale(), function($currentLocale) use ($attributes, $locale) {
            if ($locale) {
                $this->setLocale($locale);
            }

            $values = array_merge($attributes, $this->getL10nKeys());
            $this->getL10nModel()
                 ->upsert($values, [$this->getL10nForigenKeyName(), $this->getLocaleColumnName()], $attributes);
            
            $this->setLocale($currentLocale);
        });

        return $this;
    }

    /**
     * Delete model translation
     * 
     * @param string|array $locale
     * 
     * @return $this
     */
    public function deleteTranslation($locale)
    {
        $this->translations()
             ->whereIn($this->getLocaleColumnName(), is_array($locale)? $locale : [$locale])
             ->delete();

        return $this;
    }

    /**
     * Set translations for saving
     * 
     * @param array
     * @return $this
     */
    public function setTranslationsForSave($translations)
    {
        $this->withTranslations = $translations;
        return $this;
    }

    /**
     * Get translations for save
     * 
     * @return array
     */
    public function getTranslationsForSave()
    {
        return $this->withTranslations;
    }

    /**
     * Clear the model translations after saveing
     * 
     * @return $this
     */
    public function clearTranslationsForSave()
    {
        $this->withTranslations = null;
        return $this;
    }

    /**
     * Get primary/unique translation keys
     * 
     * @return array
     */
    protected function getL10nKeys()
    {
        return [
            $this->getL10nForigenKeyName() => $this->getKey(),
            $this->getLocaleColumnName() => $this->getLocale(),
        ];
    }

    /**
     * Create a new model instance for a related model.
     *
     * @param  string  $class
     * @return mixed
     */
    protected function newRelatedInstance($class)
    {
        return tap(new $class, function ($instance) {
            $instance->setTable($this->getL10nTable());

            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        return parent::newInstance($attributes, $exists)->setTranslationsForSave($this->withTranslations);
    }
}