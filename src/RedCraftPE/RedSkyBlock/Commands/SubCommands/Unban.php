<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Unban
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onUnbanCommand(CommandSender $sender, array $args): bool
    {
        if (!$sender->hasPermission("skyblock.ban")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is unban <player>");
            return true;
        }

        $senderName = strtolower($sender->getName());
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $playerName = implode(" ", array_slice($args, 1));

        // Tìm người chơi theo tên hoặc một phần tên
        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if (!$player || !$player instanceof Player || !array_key_exists($senderName, $skyblockArray)) {
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f" . $playerName . " §ckhông tồn tại hoặc không online.\n\n");
            return true;
        }

        if ($player->getName() === $sender->getName()) {
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §cBạn không bị cấm khỏi đảo mình.\n\n");
            return true;
        }

        if (in_array($player->getName(), $skyblockArray[$senderName]["Banned"] ?? [])) {
            unset($skyblockArray[$senderName]["Banned"][array_search($player->getName(), $skyblockArray[$senderName]["Banned"])]);
            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f" . $player->getName() . " §ađã được bỏ cấm vào đảo.\n\n");
            $player->sendMessage($this->plugin->NCDPrefix . "§aNgười chơi §f" . $sender->getName() . " §ađã bỏ cấm bạn vào đảo của họ.");
        } else {
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f{$player->getName()} §ckhông bị cấm vào đảo của bạn.\n\n");
        }

        return true;
    }
}
