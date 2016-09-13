<?php
namespace CoreProtect\Commands;

use CoreProtect\BaseFiles\BaseCommand;
use CoreProtect\CoreProtect;
use CoreProtect\Tasks\BackupTask;
use CoreProtect\Tasks\RestoreTask;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, September 2016
 */

class CoreProtectCommand extends BaseCommand{

    private $plugin;

    public function __construct(CoreProtect $plugin)
    {
        parent::__construct("cp", $plugin);
        $this->plugin = $plugin;
        $this->setUsage("/cp <arg|help>");
        $this->setDescription("CoreProtect Command Menu");
        $this->setAliases(array("coreprotect", "corep"));
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        $ppath = $this->plugin->getDataFolder() . "world_backups/";
        $spath = $this->plugin->getServer()->getDataPath() . "worlds/";
        if($sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "This command cannot be run in game");
            return;
        }
        if(count($args) == 0 || $args[0] == "help"){
            $sender->sendMessage(TextFormat::GREEN . "Sub-Commands for CoreProtect\n" . TextFormat::GREEN . "save|backup " . TextFormat::WHITE . "Save a world\n" . TextFormat::GREEN . "restore|r " . TextFormat::WHITE . "Restore a world\n" . TextFormat::GREEN . "list|ls " . TextFormat::WHITE . "View saved worlds\n" . TextFormat::GREEN . "info|i " . TextFormat::WHITE . "CoreProtect info");
            return;
        }
        else if($args[0] == "save" || $args[0] == "backup")
        {
            if(!isset($args[1]))
            {
                $sender->sendMessage(TextFormat::RED . "Usage: /cp save <world>");
                return;
            }
            if($args[1] == "--all"){
                $dir = scandir($this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds/");
                foreach($dir as $d){
                    if(substr($d, 0, 1) === "."){
                        continue;
                    }
                    $path = $this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $d;
                    $this->plugin->getServer()->getScheduler()->scheduleAsyncTask($task = new BackupTask($d, $path, $ppath));
                }
                return;
            }
            if(!is_dir($this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $args[1]))
            {
                $sender->sendMessage(TextFormat::RED . "Could not locate level '" . $args[1] . "'");
                return;
            }
            if(!is_dir($this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $args[1] . DIRECTORY_SEPARATOR . "region"))
            {
                $sender->sendMessage(TextFormat::RED . "Could not locate level '" . $args[1] . "'");
                return;
            }
            $path = $this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $args[1];
            $this->plugin->getServer()->getScheduler()->scheduleAsyncTask($task = new BackupTask($args[1], $path, $ppath));
        }
        else if($args[0] == "info" || $args[0] == "i"){
            $sender->sendMessage(TextFormat::GREEN . "CoreProtect v1.0.5 is a light and efficent world backup plugin created by ImagicalGamer!");
            return;
        }
        else if($args[0] == "restore" || $args[0] == "r")
        {
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::RED . "Usage: /cp restore <world>");
                return;
            }
            if(!file_exists($this->plugin->getDataFolder() . DIRECTORY_SEPARATOR . "world_backups" . DIRECTORY_SEPARATOR . $args[1] . ".zip")){
                $sender->sendMessage(TextFormat::RED . "Could not locate a backup of " . $args[1]);
                return;
            }
            if($this->plugin->getServer()->isLevelLoaded($args[1])){
                foreach($this->plugin->getServer()->getLevelByName($args[1])->getPlayers() as $p){
                    $p->kick(TextFormat::RED . "[CoreProtect]\n" . TextFormat::RED . "Force Level Unload", false);
                }
                $this->plugin->getServer()->unloadLevel($this->plugin->getServer()->getLevelByName($args[1]), true);
            }
            $path = $this->plugin->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $args[1] . ".zip";
            $this->plugin->getServer()->getScheduler()->scheduleAsyncTask($task = new RestoreTask($args[1], $spath, $ppath));
        }
        else if($args[0] == "list" || $args[0] == "ls")
        {
            $lvls = array();
            $dir = scandir($this->plugin->getDataFolder() . "world_backups/");
            foreach($dir as $d){
                if(substr($d, 0, 1) === "."){
                    continue;
                }
                array_push($lvls, $d);
            }
            $sender->sendMessage(TextFormat::GREEN . "Saved Worlds: " . count($lvls));
            foreach($lvls as $lv){
                $sender->sendMessage("- " . str_replace(".zip", "", $lv));
            }

        }
        else{
            $sender->sendMessage(TextFormat::RED . "Unknown sub-command! Try /cp help");
            return;
        }
    }
}
