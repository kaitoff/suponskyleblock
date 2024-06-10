<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use RedCraftPE\RedSkyBlock\SkyBlock;

class Custom {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onCustomCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.custom")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is custom <on/off>");
            return true;
        }

        $custom = ($args[1] === "on");
        $skyblock = $this->plugin->skyblock;

        if ($custom && empty($skyblock->get("Blocks", []))) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn phải tạo và đặt cài đặt trước đảo tùy chỉnh trước khi bật đảo tùy chỉnh.");
            return true;
        }

        $skyblock->set("Custom", $custom);
        $skyblock->save();
        $sender->sendMessage($this->plugin->NCDPrefix . "§aCustom đảo đã được " . ($custom ? "kích hoạt" : "tắt") . "!");

        return true;
    }
}
