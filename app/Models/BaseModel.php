<?php

namespace App\Models;

use App\Http\Traits\NestedRelations;
use App\Http\Traits\TranslationAccessors;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use NestedRelations;
    use TranslationAccessors;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';
}
