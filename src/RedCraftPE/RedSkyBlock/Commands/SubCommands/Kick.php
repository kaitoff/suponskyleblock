<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Kick {

    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onKickCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.kick")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        $senderName = strtolower($sender->getName());
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);

        if (!array_key_exists($senderName, $skyblockArray)) {
            $this->plugin->NCDKickForm($sender, "§l§c↣ §cBạn chưa có đảo nào cả.\n\n");
            return true;
        }

        if (count($args) < 2) {
            $this->plugin->NCDKickForm($sender, "§l§c↣ §cUsage: /is kick <player>");
            return true;
        }

        $playerName = implode(" ", array_slice($args, 1));

        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if (!$player || !$player instanceof Player) {
            $this->plugin->NCDKickForm($sender, "§l§c↣ §f" . $playerName . " §ckhông tồn tại hoặc không online.\n\n");
            return true;
        }

        if ($player->getName() === $sender->getName()) {
            $this->plugin->NCDKickForm($sender, "§l§c↣ §cBạn không thể tự đuổi mình ra khỏi đảo của bạn.\n\n");
            return true;
        }

        $playerPosition = $player->getPosition();
        $start = Position::fromObject($skyblockArray[$senderName]["Area"]["start"], $this->plugin->level);
        $end = Position::fromObject($skyblockArray[$senderName]["Area"]["end"], $this->plugin->level);

        if ($playerPosition->x > $start->x && $playerPosition->y > $start->y && $playerPosition->z > $start->z &&
            $playerPosition->x < $end->x && $playerPosition->y < $end->y && $playerPosition->z < $end->z) {
            $player->teleport($this->plugin->getServer()->getWorldManager()->getDefaultWorld()?->getSafeSpawn());
            $player->sendMessage($this->plugin->NCDPrefix . "§aNgười chơi §f" . $sender->getName() . " §ađã đuổi bạn khỏi đảo của họ.");
            $this->plugin->NCDKickForm($sender, "§l§c↣ §f" . $player->getName() . " §ađã bị đuổi khỏi đảo của bạn.\n\n");
        } else {
            $this->plugin->NCDKickForm($sender, "§l§c↣ §f" . $player->getName() . " §ckhông có trên đảo của bạn.\n\n");
        }

        return true;
    }
}
