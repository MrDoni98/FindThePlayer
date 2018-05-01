<?php
/**
 * Created by PhpStorm.
 * User: MrDoni98
 * Date: 01.05.2018
 * Time: 23:35
 */

namespace MrDoni98\FindThePlayer;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    /** @var Player[] */
    public $players = [];

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("FindThePlayer", new FindThePlayerCommand($this));
    }
}