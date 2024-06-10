<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Rank {
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onRankCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.rank")) {
            $name = strtolower($sender->getName());
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []); 

            if (array_key_exists($name, $skyblockArray)) {
                $rank = $this->plugin->calcRank($name); 
                $userCount = count($skyblockArray);

                $sender->sendMessage($this->plugin->NCDPrefix . "§aYour island is ranked " . TextFormat::WHITE . "#{$rank} " . TextFormat::GREEN . "out of {$userCount} island(s)");
                return true;
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have an island yet.");
                return true;
            }
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }
    }
}
