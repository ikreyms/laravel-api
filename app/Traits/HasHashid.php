<?php

namespace App\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

trait HasHashid
{
    protected ?Hashids $encoder;

    protected static function booted()
    {
        static::saved(function (Model $model) {
            if (empty($model->hashid)) {
                $model->hashid = $model->encodeHashid($model->{$model->getKeyName()});
                $model->save();  // Save the model with the generated hashid
            }
        });
    }

    /**
     * Define the field to be used for route model binding.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return config('hashid.field', 'hashid');  // Default to 'hashid' if the field is not set in config
    }

    /**
     * Encode the model's ID (or any other field) using Hashids.
     *
     * @param  int $id
     * @return string
     */
    public function encodeHashid($id)
    {
        return $this->getHashidEncoder()->encode($id);
    }

    /**
     * Decode the hashid to retrieve the original ID.
     *
     * @param  string $hashid
     * @return int|null
     */
    public function decodeHashid($hashid)
    {
        $decoded = $this->getHashidEncoder()->decode($hashid);
        return $decoded ? $decoded[0] : null;
    }

    /**
     * Get the model instance by hashid.
     *
     * @param string $hash
     * @return Model|null
     */
    public static function findByHashid($hash)
    {
        $instance = new static;
        $id = $instance->decodeHashid($hash);
        if ($id) {
            return static::find($id);
        }
        return null;
    }

    /**
     * Finds a model by the hashid or fails
     *
     * @param string $hash
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function findByHashidOrFail(string $hash)
    {
        $instance = new static;
        return $instance->findOrFail($instance->hashidableEncoder()->decode($hash));
    }

    /**
     * Finds a model by the hashid or fails
     *
     * @param string $hash
     * @param string $columnId By default 'id' but can be diferent some developers
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function whereHashid(string $hash, string $columnId = 'id')
    {
        $static = new static();
        return $static->where($columnId, $static->hashidableEncoder()->decode($hash));
    }

    private function getHashidEncoder()
    {
        if ($this->encoder === null) {
            $this->encoder = new Hashids(
                config('hashid.salt'),
                config('hashid.length'),
                config('hashid.chars')
            );
        }
        return $this->encoder;
    }
}
