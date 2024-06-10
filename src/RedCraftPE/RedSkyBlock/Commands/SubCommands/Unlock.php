<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Unlock
{
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onUnlockCommand(CommandSender $sender): bool
    {
        if (!$sender->hasPermission("skyblock.lock")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }

        $senderName = strtolower($sender->getName());
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []); 

        if (array_key_exists($senderName, $skyblockArray)) {
            $isLocked = $skyblockArray[$senderName]["Locked"];
            $skyblockArray[$senderName]["Locked"] = !$isLocked;
            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();

            $message = $isLocked ? "§aĐảo của bạn đã được mở khóa." : "§aĐảo của bạn đã được khóa.";
            $sender->sendMessage($this->plugin->NCDPrefix . $message);
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have an island yet.");
        }

        return true;
    }
}
