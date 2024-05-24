<?php

namespace Safadi\Tests;

use Illuminate\Database\Eloquent\Model;
use Safadi\Eloquent\L10n\Concerns\HasTranslationsModel;

class TestPost extends Model
{
    use HasTranslationsModel;

    protected $table = 'posts';
}
