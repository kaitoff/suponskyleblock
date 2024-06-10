<?php
namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;

class SetSpawn {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  
    public function onSetSpawnCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.setspawn")) {
            $senderName = strtolower($sender->getName());
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            
            if (!array_key_exists($senderName, $skyblockArray)) {
                $this->plugin->NCDMenuForm($sender, "§cBạn chưa có đảo nào cả.\n");
                return true;
            }
            
            $start = Position::fromObject($skyblockArray[$senderName]["Area"]["start"], $this->plugin->level);
            $end = Position::fromObject($skyblockArray[$senderName]["Area"]["end"], $this->plugin->level);
            
            $position = $sender->getPosition();
            $xPos = $position->getFloorX();
            $yPos = $position->getFloorY();
            $zPos = $position->getFloorZ();

            if ($xPos >= $start->x && $yPos >= $start->y && $zPos >= $start->z && 
                $xPos <= $end->x && $yPos <= $end->y && $zPos <= $end->z) {
                $skyblockArray[$senderName]["Spawn"] = $position->asVector3(); // Sử dụng asVector3()

                $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
                $this->plugin->skyblock->save();

                $sender->sendMessage($this->plugin->NCDPrefix . "§aToạ độ hồi sinh của đảo bạn đã được đặt ở §f{$xPos}§a, §f{$yPos}§a, §f{$zPos}§a.");
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn phải ở trong đảo của mình để thiết lập vị trí spawn của đảo!");
            }

            return true;
        }

        $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
        return true;
    }
}
