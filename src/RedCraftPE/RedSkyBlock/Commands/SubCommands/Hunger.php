<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Hunger
{
    private $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onHungerCommand(CommandSender $sender, array $args): bool
    {
        if (!$sender->hasPermission("skyblock.hunger")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is hunger <on/off>");
            return true;
        }

        $hunger = ($args[1] === "on"); 
        $this->plugin->cfg->set("Hunger", $hunger);
        $this->plugin->cfg->save();

        $sender->sendMessage($this->plugin->NCDPrefix . "§aHunger has been " . ($hunger ? "enabled" : "disabled") . ".");
        $this->plugin->getLogger()->info("Hunger setting changed to: " . ($hunger ? "on" : "off")); // Ghi log

        return true;
    }
}
