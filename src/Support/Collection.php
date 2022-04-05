<?php
namespace Payment\Support;
use ArrayAccess;

// use ArrayIterator;
// use Countable;
// use IteratorAggregate;
// use JsonSerializable;
// use Serializable;
// class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable{
class Collection implements ArrayAccess{

    protected $items = [];

    public function __construct(array $items = []){
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function __toString(){
        var_dump(1122);exit;
    }

    public function __get(string $key){
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        var_dump(333);exit;
        // $this->set($key, $value);
    }

    public function __isset(string $key)
    {
        // dd(111);
        return $this->get($key);
    }

    public function all(){
        var_dump(444);exit;
        // return $this->items;
    }

    public function toArray(){
        var_dump(555);exit;
        // return $this->all();
    }

    public function get(?string $key = null, $default = null){
        return Arr::get($this->items, $key, $default);
    }

    public function set(string $key, $value){
        Arr::set($this->items, $key, $value);
    }

    public function has(string $key): bool{
        return !is_null(Arr::get($this->items, $key));
    }

    public function only(array $keys): array
    {
        var_dump(555);exit;
        // $return = [];

        // foreach ($keys as $key) {
        //     $value = $this->get($key);

        //     if (!is_null($value)) {
        //         $return[$key] = $value;
        //     }
        // }

        // return $return;
    }

    public function except($keys)
    {
        var_dump(666);exit;
        // $keys = is_array($keys) ? $keys : func_get_args();

        // return new static(Arr::except($this->items, $keys));
    }

    public function merge($items): array
    {
        var_dump(666);exit;
        // foreach ($items as $key => $value) {
        //     $this->set($key, $value);
        // }

        // return $this->all();
    }

    public function first()
    {
        var_dump(666);exit;
        // return reset($this->items);
    }

    public function last()
    {
        var_dump(666);exit;
        // $end = end($this->items);

        // reset($this->items);

        // return $end;
    }

    public function getIterator()
    {
        var_dump(666);exit;
        // return new ArrayIterator($this->items);
    }

    public function add(string $key, $value)
    {
        var_dump(666);exit;
    }

    public function offsetGet($offset){
        //Support工具setDevKey方法 sandbox_signkey以数组方式调用 首先走这里
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }

    public function offsetExists($offset){
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        echo 55555;
        // if ($this->offsetExists($offset)) {
        //     $this->forget($offset);
        // }
    }

    public function offsetSet($offset, $value)
    {
        var_dump(444555);exit;
        // $this->set($offset, $value);
    }

    public function count()
    {
        echo 2525252;
        // return count($this->items);
    }

    public function jsonSerialize()
    {
        echo 202020202;
        // return $this->items;
    }

    public function serialize()
    {
        echo 2323232323;
        // return serialize($this->items);
    }

    public function unserialize($serialized)
    {
        echo 26262626;
        // return $this->items = unserialize($serialized);
    }

    public function forget(string $key)
    {
        Arr::forget($this->items, $key);
    }
}