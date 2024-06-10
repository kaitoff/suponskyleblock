<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems; 
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\player\GameMode;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate;

class Create {

    protected $plugin;
    
  public function __construct(Skyblock $plugin){
	$this->plugin = $plugin;
  }

  public function onCreateCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.create")) {
            $interval = $this->plugin->cfg->get("Interval");
            $itemsArray = $this->plugin->cfg->get("Starting Items", []);
            $levelName = $this->plugin->cfg->get("SkyBlockWorld");
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            $islands = $this->plugin->skyblock->get("Islands");
            $initialSize = $this->plugin->cfg->get("Island Size");
            $senderName = strtolower($sender->getName());

            if ($levelName === "") {
                $sender->sendMessage($this->plugin->NCDPrefix."§cBạn phải thiết lập thế giới SkyBlock để plugin này hoạt động bình thường.");
                return true;
            }

         
            $level = $this->plugin->getServer()->getWorldManager()->getWorldByName($levelName);
            if ($level === null) {
                if (!$this->plugin->getServer()->getWorldManager()->loadWorld($levelName)) {
                    $sender->sendMessage($this->plugin->NCDPrefix."§cThế giới hiện được đặt là thế giới SkyBlock không tồn tại.");
                    return true;
                }
                $level = $this->plugin->getServer()->getWorldManager()->getWorldByName($levelName); 
            }
if (array_key_exists($senderName, $skyblockArray)) {
                $this->plugin->getServer()->getCommandMap()->dispatch($sender, "is ncdgo");
                return true;
            } else {

        if (SkyBlock::getInstance()->skyblock->get("Custom")) {

          $sender->teleport(new Position($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX"), 15 + SkyBlock::getInstance()->skyblock->get("CustomY"), $islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ"), $level));
        } else {

          $sender->teleport(new Position($islands * $interval + 2, 15 + 3, $islands * $interval + 4, $level));
        }
        $sender->$sender->setSectatorMode(); 

            $this->plugin->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);

            foreach($itemsArray as $items) {
                if (count($itemsArray) > 0) {
                    $itemArray = explode(" ", $items);
                    if (count($itemArray) === 3) {
                        $id = intval($itemArray[0]);
                        $damage = intval($itemArray[1]);
                        $count = intval($itemArray[2]);
                        $sender->getInventory()->addItem(VanillaItems::get($id, $damage, $count)); 
                    }
                }
            }

        SkyBlock::getInstance()->skyblock->setNested("Islands", $islands + 1);
        $skyblockArray[$senderName] = Array(
          "Name" => "",
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
            "CraftingTable" => "on",
            "Fly" => "on",
            "Hopper" => "on",
            "Brewing" => "on",
            "Beacon" => "on",
            "Buckets" => "on",
            "PVP" => "on",
            "FlintAndSteel" => "on",
            "Furnace" => "on",
            "EnderChest" => "on"
          )
        );
        SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
        SkyBlock::getInstance()->skyblock->save();
        $sender->sendMessage($this->NCDPrefix."§aBạn đã tạo đảo thành công. Đồ đã vào túi đồ của bạn.");
        return true;
      }
} else {
            $sender->sendMessage($this->plugin->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
