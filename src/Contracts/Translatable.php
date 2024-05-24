<?php

namespace Safadi\Eloquent\L10n\Contracts;

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
     * @return Translatable
     */
    public function setLocale(string $locale): Translatable;
}