<?php

namespace App\Models;

use App\Http\Traits\NestedRelations;
use App\Http\Traits\TranslationAccessors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use NestedRelations;
    use TranslationAccessors;
    use HasFactory;
    use HasTranslations;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Override the default constructor so we can inject our custom id.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $snakeClassName = Str::of(get_class($this))
            ->afterLast('\\')
            ->snake()
            ->__toString();

        $this->append("{$snakeClassName}_id");
        $this->makeHidden('id');
    }

    /**
     * Override the custom magic method handler so we can handle the call to our method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $studlyClassName = Str::of(get_class($this))
            ->afterLast('\\')
            ->studly()
            ->__toString();

        if ($method === "get{$studlyClassName}IdAttribute") {
            return $this->id;
        }

        return parent::__call($method, $parameters);
    }
}
