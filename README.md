# StandardExtensions

###Standard Extensions for FlowyCore

## Install
```
composer require flowy/standard
```

## Listen
__Wait for any Event__
```php
<?php
namespace ListenExample;
use Flowy\Flowy;
use function StandardExtensions\listen;

use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerQuitEvent;


class ListenExample extends Flowy{
    function onEnable(){
        $this->manage($this->listenExample());
    }
    
    function listenExample(){
        $event = yield listen(PlayerJoinEvent::class);
        $this->manage($this->listenExample());
        ($player = $event->getPlayer())->sendMessage("Welcome!!");
        $filter_player = function(PlayerEvent $ev) use ($player){
            return $ev->getPlayer() === $player;
        };
    
        while(true){
            $event = yield listen(
                PlayerBedEnterEvent::class,
                PlayerBedLeaveEvent::class,
                PlayerQuitEvent::class
            )->filter($filter_player);
            
            if($event instanceof PlayerBedEnterEvent){
                $player->chat("Zzz...");
            }
            else if($event instanceof PlayerBedLeaveEvent){
                $player->chat("I slept well!!");
            }
            else if($event instanceof PlayerQuitEvent){
                $player->chat("Bye");
                break;
            }
        }
    }
}
```

## Delay
delay like sleep
```php
<?php
namespace DelayExample;

use Flowy\Flowy;
use function StandardExtensions\delay;

class DelayExample extends Flowy{
    function onEnable(){
        $this->manage($this->delayExample());
    }
    
    function delayExample(){
        $this->getLogger()->info("Start countdown!");
        for($i = 10; $i > 0; ++$i){
            $this->getLogger()->info("{$i}...");
            yield delay(20); //tick
        }
        $this->getLogger()->info("Countdown finished!");
    }
}
```