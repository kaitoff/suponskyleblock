<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\world\Position;
use pocketmine\player\GameMode;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate; 

class Create
{
    protected $plugin;
    
    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }
    
     public function onCreateCommand(CommandSender $sender): bool
    {
        if ($sender->hasPermission("skyblock.create")) {
            $interval = $this->plugin->cfg->get("Interval");
            $itemsArray = $this->plugin->cfg->get("Starting Items", []);
            $levelName = $this->plugin->cfg->get("SkyBlockWorld");
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            $islands = $this->plugin->skyblock->get("Islands");
            $initialSize = $this->plugin->cfg->get("Island Size");
            $senderName = strtolower($sender->getName());
            
            // Kiểm tra $levelName
            if (empty($levelName)) {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cVui lòng thiết lập tên thế giới SkyBlock trong config.yml.");
                return true;
            }

            $worldManager = $this->plugin->getServer()->getWorldManager();
            $level = $worldManager->getWorldByName($levelName);
            
            // Kiểm tra level
            if ($level === null) {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cThế giới SkyBlock không tồn tại hoặc chưa được tải.");
                return true;
            }

            if (array_key_exists($senderName, $skyblockArray)) {
                $this->plugin->getServer()->getCommandMap()->dispatch($sender, "is ncdgo");
                return true;
            } else {
                if (SkyBlock::getInstance()->skyblock->get("Custom")) {
                    $x = $islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX");
                    $y = 15 + SkyBlock::getInstance()->skyblock->get("CustomY");
                    $z = $islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ");
                    $sender->teleport(new Position($x, $y, $z, $level));
                } else {
                    $sender->teleport(new Position($islands * $interval + 2, 15 + 3, $islands * $interval + 4, $level));
                }
                $sender->setGamemode(GameMode::SURVIVAL()); 

                $this->plugin->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);
        
foreach ($itemsArray as $items) {
    if (count($itemsArray) > 0) {
        $itemArray = explode(":", $items);
        if (count($itemArray) >= 3) {
            $id = constant(ItemIds::class . "::" . strtoupper($itemArray[0]));
            $meta = intval($itemArray[1]);
            $count = intval($itemArray[2]);
            $item = Item::get($id, $meta, $count); 
            $sender->getInventory()->addItem($item); 
        }
    }
}






                SkyBlock::getInstance()->skyblock->setNested("Islands", $islands + 1);
                $skyblockArray[$senderName] = array(
                    "Name" => "",
                    "Members" => array($sender->getName()),
                    "Banned" => array(),
                    "Locked" => false,
                    "Value" => 0,
                    "Spawn" => array(
                        "X" => $sender->getLocation()->getX(), 
                        "Y" => $sender->getLocation()->getY(),
                        "Z" => $sender->getLocation()->getZ()
                    ),
                    "Area" => array(
                        "start" => array(
                            "X" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX")) - ($initialSize / 2),
                            "Y" => 0,
                            "Z" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ")) - ($initialSize / 2)
                        ),
                        "end" => array(
                            "X" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX")) + ($initialSize / 2),
                            "Y" => 256,
                            "Z" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ")) + ($initialSize / 2)
                        )
                    ),
                    "Settings" => array(
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
                $sender->sendMessage($this->plugin->NCDPrefix . "§aBạn đã tạo đảo thành công. Đồ đã vào túi đồ của bạn.");
                return true;
            }
       } else {
            $sender->sendMessage($this->plugin->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}