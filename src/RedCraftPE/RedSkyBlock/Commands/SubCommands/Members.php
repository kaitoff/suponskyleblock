<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Members
{

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onMembersCommand(CommandSender $sender): bool
    {
        if ($sender->hasPermission("skyblock.members")) {
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []); 
            $senderName = strtolower($sender->getName());
            if (array_key_exists($senderName, $skyblockArray)) {
                $members = implode(", ", $skyblockArray[$senderName]["Members"] ?? []); 
                $sender->sendMessage($members);
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
