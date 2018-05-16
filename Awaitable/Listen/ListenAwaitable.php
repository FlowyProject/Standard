<?php
namespace StandardExtensions\Awaitable\Listen;

use Flowy\FlowyException;
use pocketmine\event\Event;
use StandardExtensions\Awaitable\Awaitable;

if(!class_exists('StandardExtensions\Awaitable\Listen\ListenAwaitable')) {

    class ListenAwaitable extends Awaitable implements ListenAwaitableInterface
    {
        /** @var string[] */
        protected $targets = [];

        /** @var callable[] */
        protected $filters = [];

        public function filter(callable $filter) : ListenAwaitableInterface
        {
            $this->filters[] = $filter;
            return $this;
        }

        public function addListenTarget(string $event) : void
        {
            if (!is_subclass_of($event, Event::class))
                throw new FlowyException("{$event} is not an Event.");
            if (!in_array($event, $this->targets))
                $this->targets[] = $event;
        }

        /**
         * @return string[]
         */
        public function getTargetEvents() : array
        {
            return $this->targets;
        }

        /**
         * @return \Closure[]
         */
        public function getFilters() : array
        {
            return $this->filters;
        }
    }

}