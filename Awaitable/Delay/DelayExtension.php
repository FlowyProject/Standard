<?php
namespace StandardExtensions\Awaitable\Delay;

use Flowy\Extension\FlowyExtension;
use Flowy\Flow\FlowInfo;
use Flowy\Flowy;
use pocketmine\Server;

if(!class_exists('StandardExtensions\Awaitable\Delay\DelayExtension')) {

    class DelayExtension implements FlowyExtension
    {
        const NAME = 'StandardExtensions [ DelayExtension ]';
        const VERSION = '1.1.0';

        public function getName() : string
        {
            return self::NAME;
        }

        public function getVersion() : string
        {
            return self::VERSION;
        }

        public function handleReturn(Flowy $flowy, FlowInfo $flowInfo) : bool
        {
            if (!$flowInfo->getReturn() instanceof DelayAwaitable)
                return false;
            $this->schedule($flowy, $flowInfo->getFlowId(), $flowInfo->getReturn()->getDelay());
            return count(DelayAwaitable::getExtensions()) === 0; //for Awaitable extension
        }

        public function handleContinue(Flowy $flowy, FlowInfo $flowInfo) : bool
        {
            // Nothing to do
            return false;
        }

        public function handleActiveChanged(Flowy $flowy, FlowInfo $flowInfo) : bool
        {
            if ($flowInfo->isActive()) {
                return $this->handleReturn($flowy, $flowInfo);
            } else {
                return $this->handleDelete($flowy, $flowInfo);
            }
        }

        public function handleDelete(Flowy $flowy, FlowInfo $flowInfo) : bool
        {
            if (!$flowInfo->getReturn() instanceof DelayAwaitable)
                return false;
            $this->cancel($flowy, $flowInfo->getFlowId());
            return count(DelayAwaitable::getExtensions()) === 0; //for Awaitable extension
        }

        private $taskMap = [];

        private function schedule(Flowy $flowy, int $flowId, int $delay)
        {
            assert(!isset($this->taskMap[$flowId]));
            $this->taskMap[$flowId] = $flowy->getScheduler()->scheduleDelayedTask(new StdExDelayTask($flowy), $delay)->getTaskId();
        }

        private function cancel(Flowy $flowy, int $flowId)
        {
            if (isset($this->taskMap[$flowId])) {
                $flowy->getScheduler()->cancelTask($this->taskMap[$flowId]);
                unset($this->taskMap[$flowId]);
            }
        }

        public function handleDelayExMethod(Flowy $flowy, int $taskId)
        {
            if (($flowId = array_search($taskId, $this->taskMap, true)) !== false) {
                unset($this->taskMap[$flowId]);
                $flowInfo = $flowy->getFlowManager()->get($flowId);
                if ($flowInfo->continue()) {
                    $flowy->handleReturn($flowInfo);
                }
            }
        }
    }

}