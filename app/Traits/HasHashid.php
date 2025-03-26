<?php

namespace App\Traits;

use Hashids\Hashids;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasHashid
{
    protected ?Hashids $encoder = null;

    /**
     * Boot the trait to listen for the saved event on the model.
     * This ensures the `hashid` is generated when the model is saved.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saved(function (Model $model) {
            if (empty($model->hashid) && ! $model->isDirty('hashid')) {
                $model->hashid = $model->encodeHashid($model->{$model->getKeyName()});
                $model->save();
            }
        });
    }

    /**
     * Define the field to be used for route model binding.
     * 
     * Returns the field name (usually 'hashid') that should be used
     * when performing route model binding.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return config('hashid.field', 'hashid');
    }

    /**
     * Encode the model's ID (or any other field) using Hashids.
     * 
     * This method takes an ID and encodes it using the Hashids library
     * to create a short, URL-safe hashid.
     *
     * @param  int $id
     * @return string
     */
    public function encodeHashid(int $id): string
    {
        return $this->getHashidEncoder()->encode($id);
    }

    /**
     * Decode the hashid to retrieve the original ID.
     * 
     * This method decodes a given hashid and returns the corresponding
     * model's ID. If the hashid is invalid, an exception is thrown.
     *
     * @param  string $hashid
     * @return int|null
     * @throws InvalidArgumentException if the hashid is invalid
     */
    public function decodeHashid(string $hashid): ?int
    {
        $decoded = $this->getHashidEncoder()->decode($hashid);
        if (!$decoded) {
            throw new InvalidArgumentException('Invalid Hashid');
        }
        return $decoded[0];
    }

    /**
     * Get the model instance by hashid.
     * 
     * This method looks up a model by its hashid, decodes the hashid,
     * and returns the corresponding model instance.
     *
     * @param string $hash
     * @return Model|null
     */
    public static function findByHashid(string $hashid): ?Model
    {
        $instance = new static;
        $id = $instance->decodeHashid($hashid);
        if ($id) {
            return static::find($id);
        }
        return null;
    }

    /**
     * Finds a model by the hashid or fails.
     * 
     * This method tries to find a model by its hashid. If no model is
     * found for the given hashid, it throws a `ModelNotFoundException`.
     *
     * @param string $hash
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ModelNotFoundException if no model is found for the given hashid
     */
    public static function findByHashidOrFail(string $hashid): Model
    {
        $instance = new static;
        $id = $instance->decodeHashid($hashid);
        if (!$id) {
            throw new ModelNotFoundException('Model not found for the provided Hashid');
        }
        return $instance->findOrFail($id);
    }

    /**
     * Scope a query to get the model with the given hashid.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance.
     * @param string $hashid The hashid to filter the model by.
     * @return void
     */
    public function scopeOfHashid(Builder $query, string $hashid): void
    {
        $query->where(config('hashid.field'), $hashid);
    }


    /**
     * Get the Hashids encoder instance.
     * 
     * This method checks if the encoder has already been instantiated
     * and returns it. If it hasn't, it creates a new instance using
     * the configuration options.
     *
     * @return Hashids
     */
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
