<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\Island;
use RedCraftPE\RedSkyBlock\Tasks\Generate;

class Reset {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onResetCommand(CommandSender $sender): bool {
  	$this->NCDPrefix = SkyBlock::getInstance()->NCDPrefix;

    if ($sender->hasPermission("skyblock.reset")) {

      $interval = SkyBlock::getInstance()->cfg->get("Interval");
      $itemsArray = SkyBlock::getInstance()->cfg->get("Starting Items", []);
      $levelName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld");
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
      $islands = SkyBlock::getInstance()->skyblock->get("Islands");
      $initialSize = SkyBlock::getInstance()->cfg->get("Island Size");
      $senderName = strtolower($sender->getName());
      $level = null;

      if ($levelName === "") {

        $sender->sendMessage($this->NCDPrefix."§cYou must set a SkyBlock world in order for this plugin to function properly.");
        return true;
      } else {

        if (SkyBlock::getInstance()->getServer()->isLevelLoaded($levelName)) {

          $level = SkyBlock::getInstance()->getServer()->getLevelByName($levelName);
        } else {

          if (SkyBlock::getInstance()->getServer()->loadLevel($levelName)) {

            SkyBlock::getInstance()->getServer()->loadLevel($levelName);
            $level = SkyBlock::getInstance()->getServer()->getLevelByName($levelName);
          } else {

            $sender->sendMessage($this->NCDPrefix."§cThe world currently set as the SkyBlock world does not exist.");
            return true;
          }
        }
      }

      if (array_key_exists($senderName, $skyblockArray)) {

        unset($skyblockArray[$senderName]);
        SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
        SkyBlock::getInstance()->skyblock->save();
        $sender->getInventory()->clearAll();
        $sender->sendMessage($this->NCDPrefix."§aYour island has been completely reset.");

        if (SkyBlock::getInstance()->skyblock->get("Custom")) {

          $sender->teleport(new Position($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX"), 15 + SkyBlock::getInstance()->skyblock->get("CustomY"), $islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ"), $level));
        } else {

          $sender->teleport(new Position($islands * $interval + 2, 15 + 3, $islands * $interval + 4, $level));
        }
        $sender->setImmobile(true);
        SkyBlock::getInstance()->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);

        foreach($itemsArray as $items) {

          if (count($itemsArray) > 0) {

            $itemArray = explode(" ", $items);
            if (count($itemArray) === 3) {

              $id = intval($itemArray[0]);
              $damage = intval($itemArray[1]);
              $count = intval($itemArray[2]);
              $sender->getInventory()->addItem(Item::get($id, $damage, $count));
            }
          }
        }

        SkyBlock::getInstance()->skyblock->setNested("Islands", $islands + 1);
        $skyblockArray[$senderName] = Array(
          "Name" => $sender->getName() . "'s Island",
          "Members" => [$sender->getName()],
          "Banned" => [],
          "Locked" => false,
          "Value" => 0,
          "Spawn" => Array(
            "X" => $sender->getX(),
            "Y" => $sender->getY(),
            "Z" => $sender->getZ()
          ),
          "Area" => Array(
            "start" => Array(
              "X" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX")) - ($initialSize / 2),
              "Y" => 0,
              "Z" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ")) - ($initialSize / 2)
            ),
            "end" => Array(
              "X" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX")) + ($initialSize / 2),
              "Y" => 256,
              "Z" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ")) + ($initialSize / 2)
            )
          ),
          "Settings" => Array(
            "Build" => "on",
            "Break" => "on",
            "Pickup" => "on",
            "Anvil" => "on",
            "Chest" => "on",
            "CraftingTable" => "off",
            "Fly" => "on",
            "Hopper" => "on",
            "Brewing" => "off",
            "Beacon" => "on",
            "Buckets" => "off",
            "PVP" => "on",
            "FlintAndSteel" => "off",
            "Furnace" => "on",
            "EnderChest" => "on"
          )
        );
        SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
        SkyBlock::getInstance()->skyblock->save();
        return true;
      } else {

        $sender->sendMessage($this->NCDPrefix."§cYou do not have an island yet.");
        return true;
      }
    } else {

      $sender->sendMessage($this->NCDPrefix."§cYou do not have the proper permissions to run this command.");
      return true;
    }
  }
}
