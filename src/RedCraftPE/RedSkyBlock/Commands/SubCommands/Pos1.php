<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Pos1 {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  
    public function onPos1Command(CommandSender $sender): bool {
        if (!$sender->hasPermission("skyblock.pos")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }
        
        $position = $sender->getPosition();
        $xPos = $position->getFloorX();
        $yPos = $position->getFloorY();
        $zPos = $position->getFloorZ();

        $skyblock = $this->plugin->skyblock; 
        $skyblock->set("x1", $xPos);
        $skyblock->set("y1", $yPos);
        $skyblock->set("z1", $zPos);
        $skyblock->set("Pos1", true);
        $skyblock->save();

        $sender->sendMessage($this->plugin->NCDPrefix . "§aPosition 1 has been set at" . TextFormat::WHITE . " {$xPos}, {$yPos}, {$zPos}.");
        return true;
    }
}
