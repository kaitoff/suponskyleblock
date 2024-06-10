<?php
namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\item\VanillaItems;
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
            }
            
            if ($this->plugin->skyblock->get("Custom")) {
                $position = Position::fromObject(new Vector3($islands * $interval + $this->plugin->skyblock->get("CustomX"), 15 + $this->plugin->skyblock->get("CustomY"), $islands * $interval + $this->plugin->skyblock->get("CustomZ")), $level);
                $sender->teleport($position);
            } else {
                $position = Position::fromObject(new Vector3($islands * $interval + 2, 15 + 3, $islands * $interval + 4), $level);
                $sender->teleport($position);
            }

            $sender->setGamemode(GameMode::SPECTATOR());
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
            
            $senderPosition = $sender->getPosition();
            $skyblockArray[$senderName] = [
                "Name" => "",
                "Members" => [$sender->getName()],
                "Banned" => [],
                "Locked" => false,
                "Value" => 0,
                "Spawn" => $senderPosition->asVector3(),
                "Area" => [
                  "start" => (new Position(($islands * $interval + ($this->plugin->skyblock->get("CustomX") ?? 0)) - ($initialSize / 2), 0, ($islands * $interval + ($this->plugin->skyblock->get("CustomZ") ?? 0)) - ($initialSize / 2), $level))->asVector3(), // Sử dụng Position để tính toán tọa độ
                  "end" => (new Position(($islands * $interval + ($this->plugin->skyblock->get("CustomX") ?? 0)) + ($initialSize / 2), 256, ($islands * $interval + ($this->plugin->skyblock->get("CustomZ") ?? 0)) + ($initialSize / 2), $level))->asVector3(),
                ],
                "Settings" => [
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
                ]
              ];
            $this->plugin->skyblock->setNested("Islands", $islands + 1);
            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();

            $sender->sendMessage($this->plugin->NCDPrefix."§aBạn đã tạo đảo thành công. Đồ đã vào túi đồ của bạn.");
            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
