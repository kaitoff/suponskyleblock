<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class VoidClass
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onVoidCommand(CommandSender $sender, array $args): bool
    {
        if (!$sender->hasPermission("skyblock.void")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is void <on/off>");
            return true;
        }

        $void = ($args[1] === "on");
        $this->plugin->cfg->set("Void", $void);
        $this->plugin->cfg->save();

        $sender->sendMessage($this->plugin->NCDPrefix . "§aThe void has been " . ($void ? "enabled" : "disabled") . ".");
        $this->plugin->getLogger()->info("Void setting changed to: " . ($void ? "on" : "off")); 

        return true;
    }
}
