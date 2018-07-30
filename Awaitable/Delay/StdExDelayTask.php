<?php
namespace StandardExtensions\Awaitable\Delay;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;

if(!class_exists('StandardExtensions\Awaitable\Delay\StdExDelayTask')) {

    class StdExDelayTask extends Task
    {
        protected $owner;

        public function __construct(Plugin $owner){
            $this->owner = $owner;
        }

        final public function getOwner() : Plugin{
            return $this->owner;
        }

        public function onRun(int $currentTick)
        {
            $this->owner->handleDelayExMethod($this->getTaskId());
        }
    }

}