<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate;

class Create
{

    protected $plugin;

    public function __construct(Skyblock $plugin)
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

            // Kiểm tra xem người gửi lệnh có phải là người chơi không
            if (!$sender instanceof Player) {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cLệnh này chỉ có thể được sử dụng bởi người chơi.");
                return true;
            }

            // Kiểm tra xem thế giới đã được tải chưa
            $level = $this->plugin->getServer()->getWorldManager()->getWorldByName($levelName);
            if (!$level) {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cThế giới SkyBlock chưa được tải. Vui lòng thử lại sau.");
                return true;
            }

            if (array_key_exists($senderName, $skyblockArray)) {
                $this->plugin->getServer()->getCommandMap()->dispatch($sender, "is ncdgo");
                return true;
            }

            // Tính toán vị trí đảo mới
            $islandX = $islands * $interval + ($this->plugin->skyblock->get("CustomX") ?? 0);
            $islandZ = $islands * $interval + ($this->plugin->skyblock->get("CustomZ") ?? 0);
            $islandY = 15 + ($this->plugin->skyblock->get("CustomY") ?? 3); // Đặt giá trị mặc định cho CustomY là 3

            // Dịch chuyển người chơi đến đảo mới và chuyển sang chế độ Spectator
            $sender->teleport(new Position($islandX, $islandY, $islandZ, $level));
            $sender->setGamemode(GameMode::SPECTATOR());

            // Lên lịch tạo đảo sau một khoảng thời gian ngắn
            $this->plugin->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);

            // Thêm vật phẩm vào inventory
            foreach ($itemsArray as $items) {
                if (!empty($itemsArray)) {
                    $itemArray = explode(" ", $items);
                    if (count($itemArray) === 3) {
                        $id = intval($itemArray[0]);
                        $meta = intval($itemArray[1]);
                        $count = intval($itemArray[2]);
                        $sender->getInventory()->addItem(VanillaItems::get($id, $meta, $count));
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
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
