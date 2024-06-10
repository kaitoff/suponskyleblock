<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class SetWorld {

  protected $NCDPrefix = "";
  private static $instance;
  public function __construct(Skyblock $plugin){
	self::$instance = $this;
	$this->NCDPrefix = $plugin->NCDPrefix;
}
  public function onSetWorldCommand(CommandSender $sender): bool {

    if ($sender->hasPermission("skyblock.setworld")) {
      $world = $sender->getWorld()->getFolderName(); 
      SkyBlock::getInstance()->cfg->set("SkyBlockWorld", $world);
      SkyBlock::getInstance()->cfg->save();
      $sender->sendMessage($this->NCDPrefix."§f" . $world . " §ahas been set as the SkyBlock world on this server.");
      return true;
    } else {
      $sender->sendMessage($this->NCDPrefix."§cYou do not have the proper permissions to run this command.");
      return true;
    }
  }
}

