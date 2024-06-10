<?php
namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Add {

    protected $NCDPrefix = "";

    public function __construct(Skyblock $plugin) {
        $this->plugin = $plugin;
	    $this->NCDPrefix = $plugin->NCDPrefix;
    }
    public function onAddCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.members")) {
            $sender->sendMessage($this->plugin->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->plugin->NCDPrefix."§cUsage: /is add <player>");
            return true;
        }

        $senderName = strtolower($sender->getName());
        $limit = $this->plugin->cfg->get("MemberLimit");
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $playerName = implode(" ", array_slice($args, 1));

        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);

        if (!$player || !$player instanceof Player) {
            $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . $playerName . " §ckhông tồn tại hoặc không online.\n\n");
            return true;
        } else {

          if (array_key_exists($senderName, $skyblockArray)) {

            if (count($skyblockArray[$senderName]["Members"] ?? []) === $limit) {

              $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §cĐảo của bạn đã đạt đến số lượng thành viên tối đa.\n\n");
              return true;
            } else {

              if (in_array($player->getName(), $skyblockArray[$senderName]["Members"] ?? [])) {

                $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . $player->getName() . " §cđã là thành viên đảo của bạn.\n\n");
                return true;
              } else {

                if (!in_array($player->getName(), $skyblockArray[$senderName]["Banned"] ?? [])) {

                  $skyblockArray[$senderName]["Members"][] = $player->getName();
                  $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
                  $this->plugin->skyblock->save();
                  $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . $player->getName() . " §ađã được thêm vào đảo.\n\n");
                  $player->sendMessage($this->NCDPrefix."§aNgười chơi §f" . $sender->getName() . " §ađã thêm bạn vào đảo của họ.");
                  return true;
                } else {

                  $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §f" . $player->getName() . " §cđã bị ban khỏi đảo.\n\n");
                  return true;
                }
              }
            }
          } else {

            $this->plugin->NCDAddRemoveForm($sender, "§l§c↣ §cBạn chưa có đảo nào cả.\n\n");
            return true;
          }
        }
    } 
}
