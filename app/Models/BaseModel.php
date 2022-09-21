<?php

namespace App\Models;

use App\Http\Traits\NestedRelations;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use NestedRelations;
    use HasFactory;
    use HasTranslations;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public $translatable = [];
}
