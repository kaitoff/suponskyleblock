<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Pos2 {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  
    public function onPos2Command(CommandSender $sender): bool {
        if (!$sender->hasPermission("skyblock.pos")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }
        
        $position = $sender->getPosition();
        $xPos = $position->getFloorX();
        $yPos = $position->getFloorY();
        $zPos = $position->getFloorZ();

        $skyblock = $this->plugin->skyblock;
        $skyblock->set("x2", $xPos);
        $skyblock->set("y2", $yPos);
        $skyblock->set("z2", $zPos);
        $skyblock->set("Pos2", true);
        $skyblock->save();

        $sender->sendMessage($this->plugin->NCDPrefix . "§aPosition 2 has been set at" . TextFormat::WHITE . " {$xPos}, {$yPos}, {$zPos}.");
        return true;
    }
}
