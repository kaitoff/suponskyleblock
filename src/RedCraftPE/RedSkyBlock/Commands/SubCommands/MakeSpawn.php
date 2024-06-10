<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class MakeSpawn {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onMakeSpawnCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.makespawn")) {
            $skyblock = $this->plugin->skyblock; 
            
            if (!empty($skyblock->get("Blocks", []))) { 
                $xPos = $sender->getPosition()->getFloorX();
                $yPos = $sender->getPosition()->getFloorY();
                $zPos = $sender->getPosition()->getFloorZ();
    
                $x = min(0 + $skyblock->get("x1"), 0 + $skyblock->get("x2"));
                $y = $skyblock->get("y1"); 
                $z = min(0 + $skyblock->get("z1"), 0 + $skyblock->get("z2"));

                $distanceFromX1 = $xPos - $x;
                $distanceFromY1 = $yPos - $y + 1;
                $distanceFromZ1 = $zPos - $z;

                $skyblock->set("CustomX", $distanceFromX1);
                $skyblock->set("CustomY", $distanceFromY1);
                $skyblock->set("CustomZ", $distanceFromZ1);
                $skyblock->save();

                $sender->sendMessage($this->plugin->NCDPrefix . "§aToạ độ hồi sinh của đảo bạn đã được đặt ở §f{$distanceFromX1}§a, §f{$distanceFromY1}§a, §f{$distanceFromZ1}§a.");

                return true;
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn phải tạo và đặt giá trị đặt trước đảo tùy chỉnh trước khi tạo điểm spawn đảo tùy chỉnh.");
                return true;
            }
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
