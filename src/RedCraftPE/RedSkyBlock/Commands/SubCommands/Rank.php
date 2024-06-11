<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use RedCraftPE\RedSkyBlock\Commands\Island;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Rank {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onRankCommand(CommandSender $sender): bool {
  	$this->NCDPrefix = SkyBlock::getInstance()->NCDPrefix;

    if ($sender->hasPermission("skyblock.rank")) {

      $name = strtolower($sender->getName());
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);

      if (array_key_exists($name, $skyblockArray)) {

        $rank = SkyBlock::getInstance()->calcRank($name);
        $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
        $userCount = count($skyblockArray);

        $sender->sendMessage("§l§cSkyBlock §e↣ §aYour island is ranked " . TextFormat::WHITE . "#{$rank} " . TextFormat::GREEN . "out of {$userCount} island(s)");
        return true;
      } else {

        $sender->sendMessage("§l§cSkyBlock §e↣ §cYou do not have an island yet.");
        return true;
      }
    } else {

      $sender->sendMessage("§l§cSkyBlock §e↣ §cYou do not have the proper permissions to run this command.");
      return true;
    }
  }
}
