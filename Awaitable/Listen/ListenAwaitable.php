<?php
namespace StandardExtensions\Awaitable\Listen;

use Flowy\FlowyException;
use pocketmine\event\Event;
use StandardExtensions\Awaitable\Awaitable;

if(!class_exists('StandardExtensions\Awaitable\Listen\ListenAwaitable')) {

    class ListenAwaitable extends Awaitable
    {
        /** @var string[] */
        protected $targets = [];

        /** @var callable[] */
        protected $filters = [];

        public function filter(callable $filter)
        {
            $this->filters[] = $filter;
            return $this;
        }

        public function addListenTarget(string $event)
        {
            if (!is_subclass_of($event, Event::class))
                throw new FlowyException("{$event} is not an Event.");
            if (!in_array($event, $this->targets))
                $this->targets[] = $event;
        }

        public function getTargetEvents()
        {
            return $this->targets;
        }

        public function getFilters()
        {
            return $this->filters;
        }
    }

}