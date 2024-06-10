<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Remove {
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onRemoveCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.members")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }

        $senderName = strtolower($sender->getName());
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []); 
        if (count($args) < 2) {
            $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §cUsage: /is remove <player>");
            return true;
        }
        
        $playerName = implode(" ", array_slice($args, 1));

        
        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if (!$player || !$player instanceof Player || !array_key_exists($senderName, $skyblockArray) || !in_array($player->getName(), $skyblockArray[$senderName]["Members"])) {
          $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . implode(" ", array_slice($args, 1)) . " §ckhông tồn tại hoặc không online.\n\n");
          return true;
        }
        if ($player->getName() === $sender->getName()) {
            $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §cBạn không thể xóa bạn khỏi đảo của bạn.\n\n");
            return true;
        }

        unset($skyblockArray[$senderName]["Members"][array_search($player->getName(), $skyblockArray[$senderName]["Members"])]);

        $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
        $this->plugin->skyblock->save();

        $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . $player->getName() . " §ađã được xóa khỏi đảo.\n\n");

        return true;
    }
}
