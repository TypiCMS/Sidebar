<?php

namespace TypiCMS\Modules\Sidebar\Traits;

use Illuminate\Support\Str;

trait Attributable
{
    /** @var array<string, mixed> */
    protected array $attributes = [];

    public function cleanInstance(): self
    {
        return $this->container->make(get_class($this));
    }

    public function setAttribute(string $attribute, mixed $value): self
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function getAttribute(string $attribute, ?string $value = null): mixed
    {
        $value = $this->getRawAttribute($attribute, $value);

        if ($this->hasMutator($attribute)) {
            return $this->mutate($attribute, $value);
        }

        return $value;
    }

    public function getRawAttribute(string $attribute, ?string $value = null): mixed
    {
        if (isset($this->attributes[$attribute])) {
            $value = $this->attributes[$attribute];
        }

        return $value;
    }

    public function hasMutator(string $attribute): bool
    {
        $method = $this->getMutateMethod($attribute);

        return method_exists($this, $method);
    }

    public function mutate(string $attribute, ?string $value): mixed
    {
        $method = $this->getMutateMethod($attribute);

        return $this->{$method}($value);
    }

    protected function getMutateMethod(string $attribute): string
    {
        return 'get' . Str::studly($attribute);
    }

    public function __set(string $attribute, ?string $value): void
    {
        $this->setAttribute($attribute, $value);
    }

    public function __get(string $attribute): ?string
    {
        return $this->getAttribute($attribute);
    }

    public function __isset(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function __call(string $method, mixed $params): self
    {
        return $this->setAttribute($method, head($params));
    }
}
