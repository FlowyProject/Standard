<?php
namespace StandardExtensions\Awaitable\Delay;

use pocketmine\scheduler\PluginTask;

if(!class_exists('StandardExtensions\Awaitable\Delay\StdExDelayTask')) {

    class StdExDelayTask extends PluginTask
    {
        public function onRun(int $currentTick)
        {
            $this->owner->handleDelayExMethod($this->getTaskId());
        }
    }

}