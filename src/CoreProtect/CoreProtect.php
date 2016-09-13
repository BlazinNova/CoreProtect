<?php
namespace CoreProtect;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

use CoreProtect\Commands\CoreProtectCommand;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, September 2016
 */

class CoreProtect extends PluginBase implements Listener{
  
  public function onEnable()
  {
    @mkdir($this->getDataFolder());
    @mkdir($this->getDataFolder() . "world_backups");
    $this->getServer()->getPluginManager()->registerEvents($this ,$this);
    $this->getServer()->getCommandMap()->register("cp", new CoreProtectCommand($this));
    date_default_timezone_set("UTC");
  }
}
