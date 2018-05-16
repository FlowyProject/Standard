<?php
namespace StandardExtensions\Awaitable\Delay;

interface DelayAwaitableInterface
{
    public function setDelay(int $delay) : void;

    public function getDelay() : int;
}