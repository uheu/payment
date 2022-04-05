<?php

declare(strict_types=1);

namespace Payment\Supports\Traits;

// use ReflectionClass;
// use Yansongda\Supports\Str;

trait Arrayable
{
    /**
     * toArray.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        var_dump(5666);exit;
        // $result = [];

        // foreach ((new ReflectionClass($this))->getProperties() as $item) {
        //     $k = $item->getName();
        //     $method = 'get'.Str::studly($k);

        //     $result[Str::snake($k)] = method_exists($this, $method) ? $this->{$method}() : $this->{$k};
        // }

        // return $result;
    }
}
