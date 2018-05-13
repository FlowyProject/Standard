<?php
namespace StandardExtensions\Awaitable\Listen;

use Flowy\Extension\FlowyExtension;
use Flowy\Flow\FlowInfo;
use Flowy\Flowy;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerList;
use pocketmine\event\Event;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\PluginException;
use pocketmine\plugin\PluginManager;
use pocketmine\plugin\RegisteredListener;
use pocketmine\timings\TimingsHandler;

if(!class_exists('StandardExtensions\Awaitable\Listen\ListenExtension')) {

    class ListenExtension implements FlowyExtension
    {
        const NAME = 'StandardExtensions [ ListenExtension ]';
        const VERSION = '1.0.0';

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
            if (!$flowInfo->getReturn() instanceof ListenAwaitable)
                return false;
            foreach ($flowInfo->getReturn()->getTargetEvents() as $event) {
                $this->registerEvent($flowy, $flowInfo->getFlowId(), $event);
            }
            return count(ListenAwaitable::getExtensions()) === 0; //for Awaitable extension
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
            if (!$flowInfo->getReturn() instanceof ListenAwaitable)
                return false;
            foreach ($flowInfo->getReturn()->getTargetEvents() as $class) {
                $this->unregisterEvent($flowInfo->getFlowId(), $class);
            }
            return count(ListenAwaitable::getExtensions()) === 0; //for Awaitable extension
        }

        private $registeredListeners = [];

        private function registerEvent(Flowy $flowy, int $flowId, string $event)
        {
            if (!isset($this->registeredListeners[$event])) {
                $plugin_info = "Plugin: " . $flowy->getDescription()->getFullName();
                $event_name = (new \ReflectionClass($event))->getShortName();
                $timings = new TimingsHandler("{$plugin_info}, ListenExtension::registerEvent({$event_name})", PluginManager::$pluginParentTimer);
                $executor = new MethodEventExecutor('handleListenExMethod');
                $listener = new RegisteredListener($flowy, $executor, EventPriority::NORMAL, $flowy, false, $timings);
                $this->registeredListeners[$event] = [
                    'listener' => $listener,
                    'listeners' => []
                ];
                $this->getEventListeners($event)->register($listener);
            }
            assert(!in_array($flowId, $this->registeredListeners[$event]['listeners'], true));
            $this->registeredListeners[$event]['listeners'][] = $flowId;
        }

        private function unregisterEvent(int $flowId, string $event)
        {
            if (!isset($this->registeredListeners[$event]))
                return;
            if (($index = array_search($flowId, $this->registeredListeners[$event]['listeners'], true)) !== false) {
                unset($this->registeredListeners[$event]['listeners'][$index]);
                if (count($this->registeredListeners[$event]['listeners']) === 0) {
                    $this->getEventListeners($event)->unregister($this->registeredListeners[$event]['listener']);
                    unset($this->registeredListeners[$event]);
                }
            }
        }

        private function getEventListeners(string $event) : HandlerList
        {
            $list = HandlerList::getHandlerListFor($event);
            if ($list === null) {
                throw new PluginException("Abstract events not declaring @allowHandle cannot be handled (tried to register listener for $event)");
            }
            return $list;
        }

        public function handleListenExMethod(Flowy $flowy, Event $event)
        {
            foreach ($flowy->getFlowRepository()->getIterator() as $flowInfo) {
                if (!$flowInfo->getReturn() instanceof ListenAwaitable)
                    continue;
                if (!in_array(get_class($event), $flowInfo->getReturn()->getTargetEvents()))
                    continue;
                foreach ($flowInfo->getReturn()->getTargetEvents() as $class) {
                    $this->unregisterEvent($flowInfo->getFlowId(), $class);
                }
                if ($flowInfo->continue($event)) {
                    $flowy->handleReturn($flowInfo);
                }
            }
        }
    }

}