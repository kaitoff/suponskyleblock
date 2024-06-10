<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Reload {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  
    public function onReloadCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.reload")) {
            $this->plugin->skyblock->reload();
            $this->plugin->cfg->reload();
            $sender->sendMessage($this->plugin->NCDPrefix."§fAll SkyBlock data has been reloaded.");
            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix."§cYou do not have the proper permissions to run this command.");
            return true;
        }
    }
}
