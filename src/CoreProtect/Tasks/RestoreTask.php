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

class RestoreTask extends AsyncTask{

  public function __construct(String $name, String $spath, String $ppath)
  {
    $this->name = $name;
    $this->spath = $spath;
    $this->ppath = $ppath;
  }

  public function file_deleteDir($dir)
  {
    $dir = rtrim($dir, "/\\") . "/";
    foreach(scandir($dir) as $file)
    {
      if($file == "." or $file === "..")
      {
        continue;
      }
      $path = $dir . $file;
        if(is_dir($path))
        {
          $this->file_deleteDir($path);
        } 
        else
        {
          unlink($path);
        }
    }
    rmdir($dir);
  }

  public function onRun()
  {
    $this->file_deleteDir($this->spath . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR);
    @mkdir($this->spath . DIRECTORY_SEPARATOR . $this->name);
    $zip = new \ZipArchive;
    $zip->open($this->ppath . DIRECTORY_SEPARATOR . $this->name . ".zip");
    $zip->extractTo($this->spath . DIRECTORY_SEPARATOR . $this->name);
    $zip->close();
    }


  public function onCompletion(Server $server)
  {
    echo(TextFormat::toANSI(TextFormat::AQUA . "[" . date("H:i:s", time()) . "] " . TextFormat::RESET . TextFormat::WHITE . "[Server thread/INFO]: [CoreProtect] " . TextFormat::GREEN . "Level '" . $this->name . "' has been restored!\n" . TextFormat::WHITE));
    $server->loadLevel($this->name);
  }
}
