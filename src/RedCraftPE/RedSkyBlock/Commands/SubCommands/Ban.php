<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Ban {
  protected $plugin;
  public function __construct(Skyblock $plugin) {
      $this->plugin = $plugin;
  }

  public function onBanCommand(CommandSender $sender, array $args): bool {
    if ($sender->hasPermission("skyblock.ban")) {
      if (count($args) < 2) {
        $sender->sendMessage($this->plugin->NCDPrefix."§cUsage: /is ban <player>");
        return true;
      } else {
        $senderName = strtolower($sender->getName());
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $playerName = implode(" ", array_slice($args, 1));

        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if (!$player || !$player instanceof Player) { 
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f" . $playerName . " §ckhông tồn tại hoặc không online.\n\n");
            return true;
        }

        if ($player->getName() === $sender->getName()) {
          $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §cBạn không thể tự cấm mình.\n\n");
          return true;
        }

        if (array_key_exists($senderName, $skyblockArray)) {
            if (in_array($player->getName(), $skyblockArray[$senderName]["Banned"] ?? [])) {
                $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f{$player->getName()}" . " §cđã bị cấm khỏi đảo của bạn.\n\n");
                return true;
            } 

            $skyblockArray[$senderName]["Banned"][] = $player->getName();
            if (in_array($player->getName(), $skyblockArray[$senderName]["Members"])) {
                unset($skyblockArray[$senderName]["Members"][array_search($player->getName(), $skyblockArray[$senderName]["Members"])]);
            }
            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §f" . $player->getName() . " §ađã được cấm khỏi đảo của bạn.\n\n");
            $player->sendMessage($this->plugin->NCDPrefix."§aNgười chơi §f" . $sender->getName() . " §ađã cấm bạn vào đảo của họ.");
            return true;
        } else {
            $this->plugin->NCDBanUnBanForm($sender, "§l§c↣ §cBạn chưa có đảo nào cả.\n\n");
            return true;
        }

      }
    } else {
      $sender->sendMessage($this->plugin->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
      return true;
    }
  }
}
