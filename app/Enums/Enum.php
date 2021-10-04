<?php

namespace App\Enums;

abstract class Enum {


    protected static $cache = [];

    // Enumeration constructor 
    final public function __construct($value) { 
        $this->value = $value; 
    } 
    
    // String representation 
    final public function __toString() { 
        return $this->value; 
    } 

    public static function toArray()
    {
        $class = static::class;

        if (!isset(static::$cache[$class])) {
            $reflection            = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        return static::$cache[$class];
    }
    
    public static function search($value)
    {
        return \array_search($value, static::toArray(), true);
    }
    
}