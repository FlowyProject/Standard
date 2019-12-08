<?php
namespace flowy\standard;

use pocketmine\event\Event;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use function flowy\listen;

if(!defined("flowy_standard_Delay")) {
    define("flowy_standard_Delay", 1);

    class DelayCallbackEvent extends Event
    {
        /** @var int */
        protected $id;

        public function __construct(int $id)
        {
            $this->id = $id;
        }

        public function getDelayId(): int
        {
            return $this->id;
        }
    }

    class DelayTask extends Task
    {
        protected static $currentId = 0;
        /** @var int */
        protected $id;

        protected function __construct(int $id)
        {
            $this->id = $id;
        }

        public function getDelayId(): int
        {
            return $this->id;
        }

        public function onRun(int $currentTick)
        {
            (new DelayCallbackEvent($this->id))->call();
        }

        public static function create(): DelayTask
        {
            return new DelayTask(self::$currentId++);
        }
    }

    function delay(TaskScheduler $scheduler, int $tick)
    {
        $task = DelayTask::create();
        $scheduler->scheduleDelayedTask($task, $tick);
        yield listen(DelayCallbackEvent::class)->filter(function ($ev) use ($task) {
            return $ev->getDelayId() === $task->getDelayId();
        });
    }

}