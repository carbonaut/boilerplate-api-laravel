<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\ResolveRouteBinding;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;
    use ResolveRouteBinding;

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
