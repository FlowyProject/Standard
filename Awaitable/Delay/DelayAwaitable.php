<?php
namespace StandardExtensions\Awaitable\Delay;

use Flowy\FlowyException;
use StandardExtensions\Awaitable\Awaitable;

if(!class_exists('StandardExtensions\Awaitable\Delay\DelayAwaitable')) {

    class DelayAwaitable extends Awaitable
    {
        /** @var int */
        protected $delay;

        public function setDelay(int $delay) : void
        {
            if ($delay <= 0)
                throw new FlowyException("Tick number must be a value of 1 or more.");
            $this->delay = $delay;
        }

        public function getDelay() : int
        {
            return $this->delay;
        }
    }

}