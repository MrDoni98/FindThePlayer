<?php
/**
 * Created by PhpStorm.
 * User: MrDoni98
 * Date: 01.05.2018
 * Time: 23:36
 */

namespace MrDoni98\FindThePlayer;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;

class EventListener implements Listener
{
    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onMove(PlayerMoveEvent $event){
        $wanted = $event->getPlayer();
        if(($key = array_search($wanted, $this->plugin->players)) !== false){
            $finder = $this->plugin->getServer()->getPlayerExact($key);
            $pk = new SetSpawnPositionPacket();
            $pk->x = $wanted->getFloorX();
            $pk->y = $wanted->getFloorY();
            $pk->z = $wanted->getFloorZ();
            $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;//1
            $finder->dataPacket($pk);
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(isset($this->plugin->players[$player->getName()])){
            unset($this->plugin->players[$player->getName()]);
        }
        if(($key = array_search($player, $this->plugin->players)) !== false){
            $finder = $this->plugin->getServer()->getPlayerExact($key);
            $finder->sendMessage("Wanted player to leave the server");
            unset($this->plugin->players[$finder->getName()]);
        }
    }
}