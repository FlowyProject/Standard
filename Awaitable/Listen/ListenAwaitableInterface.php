<?php
namespace StandardExtensions\Awaitable\Listen;

interface ListenAwaitableInterface
{
    public function filter(callable $filter) : ListenAwaitableInterface;

    public function addListenTarget(string $event) : void;

    public function getTargetEvents() : array;

    public function getFilters() : array;
}