<?php
/**
 * Created by PhpStorm.
 * User: MrDoni98
 * Date: 01.05.2018
 * Time: 23:44
 */

namespace MrDoni98\FindThePlayer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FindThePlayerCommand extends Command
{
    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("findplayer", "search for a player with a compass", "Usage: /findplayer <player>", []);
        $this->setPermission("findtheplayer.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$this->testPermission($sender)){
            return true;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED."Use command in game!");
            return false;
        }
        if(count($args) === 0){
            if(isset($this->plugin->players[$sender->getName()])){
                unset($this->plugin->players[$sender->getName()]);
                $pk = new SetSpawnPositionPacket();
                $location = $sender->getLevel()->getSpawnLocation();
                $pk->x = $location->getFloorX();
                $pk->y = $location->getFloorY();
                $pk->z = $location->getFloorZ();
                $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;//1
                $sender->dataPacket($pk);
                $sender->sendMessage("Search Finished");
                return true;
            }else{
                $sender->sendMessage($this->getUsage());
                return true;
            }
        }else{
            if(!is_null($wanted = $this->plugin->getServer()->getPlayerExact($args[1]))){
                $this->plugin->players[$sender->getName()] = $wanted;
                $pk = new SetSpawnPositionPacket();
                $pk->x = $wanted->getFloorX();
                $pk->y = $wanted->getFloorY();
                $pk->z = $wanted->getFloorZ();
                $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;//1
                $sender->dataPacket($pk);
                $sender->sendMessage("The search for the player ".$wanted->getName()." began. Pick up the compass and follow the arrow.");
                $sender->sendMessage("Enter /findplayer to terminate");
                return true;
            }else{
                $sender->sendMessage("Player not found");
                return false;
            }
        }
    }
}