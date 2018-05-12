<?php
namespace StandardExtensions;

use Flowy\Flowy;
use Flowy\FlowyException;
use StandardExtensions\Awaitable\Delay\DelayAwaitable;
use StandardExtensions\Awaitable\Delay\DelayExtension;
use StandardExtensions\Awaitable\Listen\ListenAwaitable;
use StandardExtensions\Awaitable\Listen\ListenExtension;

if(!Flowy::isInstalled(ListenExtension::class)) {
    Flowy::install(ListenExtension::class);
}

if(!Flowy::isInstalled(DelayExtension::class)) {
    Flowy::install(DelayExtension::class);
}

if(!function_exists('StandardExtensions\listen')) {
    function listen(string ...$events) : ListenAwaitable
    {
        if (count($events) === 0)
            throw new FlowyException("Please specify at least one event to wait.");
        $awaitable = new ListenAwaitable();
        foreach ($events as $event)
            $awaitable->addListenTarget($event);
        return $awaitable;
    }
}

if(!function_exists('StandardExtensions\delay')) {
    function delay(int $delay) : DelayAwaitable
    {
        $awaitable = new DelayAwaitable();
        $awaitable->setDelay($delay);
        return $awaitable;
    }
}