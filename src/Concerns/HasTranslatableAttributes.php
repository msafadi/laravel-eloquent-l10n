<?php

namespace Safadi\Eloquent\L10n\Concerns;

use Safadi\Eloquent\L10n\Casts\L10n;

trait HasTranslatableAttributes
{
    use HasLocale;
    
    /**
     * Initialize the trait.
     *
     * @return void
     */
    protected function initializeHasTranslatableAttributes()
    {
        foreach ($this->translatableAttributes() as $key) {
            $casts[$key] = L10n::class;
        }
        $this->mergeCasts($casts);
    }

    protected function translatableAttributes()
    {
        return [];
    }

    protected function isL10nAttribute($key)
    {
        return in_array($key, $this->translatableAttributes());
    }

    protected function localizeAttribute($key, $value)
    {
        $value = $this->castAttribute($key, $value);
        if ($value) {
            return $value[$this->getLocale()];
        }
        return $value;
    }

    protected function setL10nAttribute($key, $value)
    {
        return $this->castAttributeAsJson($key, $value);
    }
}