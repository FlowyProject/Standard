<?php
namespace StandardExtensions\Awaitable;

use Flowy\FlowyException;

if(!class_exists('StandardExtensions\Awaitable\Awaitable')) {

    abstract class Awaitable
    {
        static private $extensionMethods = [];

        static public function registerMethod(string $name, \Closure $method)
        {
            if (isset(self::$extensionMethods[$name]))
                throw new FlowyException("{$name} is already registered.");

            self::$extensionMethods[$name] = [
                'extend' => get_called_class(),
                'method' => $method
            ];
        }

        public function __call($name, $args)
        {
            if (!isset(self::$extensionMethods[$name]) || !is_a($this, self::$extensionMethods[$name]['extend']))
                throw new FlowyException("Unknown method {$name}");

            (self::$extensionMethods[$name]['method']->bindTo($this))(...$args);
            return $this;
        }

        static public function getExtensions()
        {
            $extensions = [];
            foreach (self::$extensionMethods as $name => $extension) {
                if (is_a(get_called_class(), $extension['extend'], true)) {
                    $extensions[$name] = $extension['method'];
                }
            }
            return $extensions;
        }
    }

}