<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Fly {

    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onFlyCommand(CommandSender $sender): bool {
        if (!$sender->hasPermission("skyblock.fly")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if ($sender instanceof Player && $sender->getWorld()->getFolderName() === $this->plugin->cfg->get("SkyBlockWorld")) {
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            $playerPosition = $sender->getPosition();
            $islandOwner = "";

            foreach (array_keys($skyblockArray) as $skyblocks) {
                $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->plugin->level);
                $end = Position::fromObject($skyblockArray[$skyblocks]["Area"]["end"], $this->plugin->level);
                if ($playerPosition->x > $start->x && $playerPosition->y > $start->y && $playerPosition->z > $start->z && 
                    $playerPosition->x < $end->x && $playerPosition->y < $end->y && $playerPosition->z < $end->z) {
                    $islandOwner = $skyblocks;
                    break;
                }
            }

            $canFly = ($islandOwner === "" || in_array($sender->getName(), $skyblockArray[$islandOwner]["Members"]) || $skyblockArray[$islandOwner]["Settings"]["Fly"] === "on");

            $sender->setAllowFlight($canFly); 
            $message = $canFly ? "§aFlight has been enabled." : "§cThe owner of this island has disabled flight here.";
            $sender->sendMessage($this->plugin->NCDPrefix . $message); 

            return true;
        }

        $sender->sendMessage($this->plugin->NCDPrefix . "§cYou must be in the SkyBlock world to use this command.");
        return true;
    }
}
