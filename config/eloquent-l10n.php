<?php

return [
    /**
     * Postfix of translations tables, posts_l10n, products_l10n, ...
     */
    'table_postfix' => '_l10n',

    /**
     * Column name of the locale attribute in the the translations table
     */
    'locale_key' => 'locale',

    /**
     * Translation Model
     */
    'model' => \Safadi\Eloquent\L10n\L10n::class,
];