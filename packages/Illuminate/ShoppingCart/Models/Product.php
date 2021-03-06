<?php

namespace PhpSoft\Illuminate\ShoppingCart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Exception;

use Webpatser\Uuid\Uuid;
use Illuminate\Support\Str;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'title', 'alias', 'image', 'description', 'price', 'galleries'];

    /**
     * Create Product
     * 
     * @param  array  $attributes        Attributes
     * @return PhpSoft\Illuminate\ShoppingCart\Model\Product
     */
    public static function create(array $attributes = [])
    {
        if (empty($attributes['alias'])) {
            $attributes['alias'] = Str::slug($attributes['title'])
                .'-'.Uuid::generate(4);
        }

        if (empty($attributes['galleries'])) {
            $attributes['galleries'] = [];
        }
        $attributes['galleries'] = json_encode($attributes['galleries']);

        return parent::create($attributes)->fresh();
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @return bool|int
     */
    public function update(array $attributes = [])
    {
        if (!empty($attributes['galleries'])) {
            $attributes['galleries'] = json_encode($attributes['galleries']);
        }

        if (!parent::update($attributes)) {
            throw new Exception('Cannot update product.');
        }

        return $this;
    }

    /**
     * List
     * 
     * @param  array  $options
     * @return array
     */
    public static function browse($options = [])
    {
        if (empty($options)) {
            return parent::all();
        }

        if (!empty($options['order'])) {
            foreach ($options['order'] as $field => $direction) {
                $find = parent::orderBy($field, $direction);
            }
        }

        if (!empty($options['limit'])) {
            $find = $find->take($options['limit']);
        }

        if (!empty($options['cursor'])) {
            $find = $find->where('id', '<', $options['cursor']);
        }

        return $find->get();
    }
}
