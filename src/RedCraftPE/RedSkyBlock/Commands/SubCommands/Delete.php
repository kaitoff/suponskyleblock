<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Delete
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onDeleteCommand(CommandSender $sender, array $args): bool
    {
        if (!$sender->hasPermission("skyblock.delete")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is delete <player>");
            return true;
        }

        $playerName = strtolower(implode(" ", array_slice($args, 1)));
        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if ($player instanceof Player) {
            $player->teleport($this->plugin->getServer()->getWorldManager()->getDefaultWorld()?->getSafeSpawn()); 
            $player->sendMessage($this->plugin->NCDPrefix . "§cYour island has been deleted by a server administrator");
        }

        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);

        if (array_key_exists($playerName, $skyblockArray)) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§aYou have successfully deleted §f" . $skyblockArray[$playerName]["Members"][0] . "'s§a island.");
            unset($skyblockArray[$playerName]);

            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();
            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cThis player does not have an island to delete.");
            return true;
        }
    }
}
