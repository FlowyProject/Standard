<?php
namespace StandardExtensions\Awaitable\Delay;

use Flowy\Flowy;
use pocketmine\scheduler\Task;

if(!class_exists('StandardExtensions\Awaitable\Delay\StdExDelayTask')) {

    class StdExDelayTask extends Task
    {
        /** @var Flowy */
        private $flowy;

        public function __construct(Flowy $flowy)
        {
            $this->flowy = $flowy;
        }

        public function onRun(int $currentTick)
        {
            $this->flowy->handleDelayExMethod($this->getTaskId());
        }
    }

}