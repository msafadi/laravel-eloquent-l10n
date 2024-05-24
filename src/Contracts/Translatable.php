<?php

namespace Safadi\Eloquent\L10n\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Translatable
{
    /**
     * Get model locale
     * 
     * @return string
     */
    public function getLocale(): string;

    /**
     * Set model locale
     * 
     * @param string $locale
     * @return  Model|Translatable
     */
    public function setLocale(string $locale): Model|Translatable;
}