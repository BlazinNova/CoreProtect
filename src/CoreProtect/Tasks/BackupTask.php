<?php
namespace CoreProtect\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use CoreProtect\CoreProtect;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, September 2016
 */

class BackupTask extends AsyncTask{

  public function __construct(String $name, String $path, String $ppath)
  {
  	$this->path = $path;
    $this->name = $name;
    $this->ppath = $ppath;
  }

  public function onRun()
  {
  	if(file_exists($this->ppath . $this->name . ".zip")){
  		echo(TextFormat::toANSI(TextFormat::AQUA . "[" . date("H:i:s", time()) . "] " . TextFormat::RESET . TextFormat::YELLOW . "[Server thread/WARNING]: [CoreProtect] Level '" . $this->name . "' has previously been saved! Overwriting...\n" . TextFormat::WHITE));
  	}
  	$zip = new \ZipArchive;
  	$zip->open($this->ppath . $this->name . ".zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path)) as $file) {
              if(is_file($file)) {
                    $zip->addFile($file, str_replace("\\", "/", ltrim(substr($file, strlen($this->path)), "/\\")));
            }
      }
    $zip->close();
  }
  
  public function onCompletion(Server $server){
  	echo(TextFormat::toANSI(TextFormat::AQUA . "[" . date("H:i:s", time()) . "] " . TextFormat::RESET . TextFormat::WHITE . "[Server thread/INFO]: [CoreProtect] " . TextFormat::GREEN . "Level '" . $this->name . "' has been saved!\n" . TextFormat::WHITE));
  }
}
