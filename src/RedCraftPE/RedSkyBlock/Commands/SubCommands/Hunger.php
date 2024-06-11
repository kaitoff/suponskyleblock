<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\Island;

class Hunger {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onHungerCommand(CommandSender $sender, array $args): bool {
  	$this->NCDPrefix = SkyBlock::getInstance()->NCDPrefix;

    if ($sender->hasPermission("skyblock.hunger")) {

      $hunger = SkyBlock::getInstance()->cfg->get("Hunger");

      if (count($args) < 2) {

        $sender->sendMessage($this->NCDPrefix."§cUsage: /is hunger <on/off>");
        return true;
      } else {

        if ($args[1] === "on") {

          $hunger = "on";
          SkyBlock::getInstance()->cfg->set("Hunger", $hunger);
          SkyBlock::getInstance()->cfg->save();
          $sender->sendMessage($this->NCDPrefix."§aHunger has been enabled.");
          return true;
        } else if ($args[1] === "off") {

          $hunger = "off";
          SkyBlock::getInstance()->cfg->set("Hunger", $hunger);
          SkyBlock::getInstance()->cfg->save();
          $sender->sendMessage($this->NCDPrefix."§aHunger has been disabled.");
          return true;
        } else {

          $sender->sendMessage($this->NCDPrefix."§cUsage: /is hunger <on/off>");
          return true;
        }
      }
    } else {

      $sender->sendMessage($this->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
      return true;
    }
  }
}
