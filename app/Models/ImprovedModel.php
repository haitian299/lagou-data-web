<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: haitian
 * Date: 16/4/14
 * Time: 10:49
 */

abstract class ImprovedModel extends Model
{
    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array $attributes
     *
     * @return static
     */
    public static function firstOrCreate(array $attributes)
    {
        if (!is_null($instance = static::where($attributes)->first())) {
            return $instance;
        }

        return static::create($attributes);
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array $attributes
     *
     * @return static
     */
    public static function firstOrNew(array $attributes)
    {
        if (!is_null($instance = static::where($attributes)->first())) {
            return $instance;
        }

        return new static($attributes);
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array $attributes
     * @param  array $values
     *
     * @return static
     */
    public static function updateOrCreate(array $attributes, array $values = array())
    {
        $instance = static::firstOrNew($attributes);

        $instance->fill($values)->save();

        return $instance;
    }

    public static function createAll(array $values)
    {
        return \DB::table(with(new static)->table)->insert($values);
    }
}