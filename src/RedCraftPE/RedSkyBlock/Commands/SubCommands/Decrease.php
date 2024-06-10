<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Decrease
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onDecreaseCommand(CommandSender $sender, array $args): bool
    {
        if (!$sender->hasPermission("skyblock.size")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 3 || !is_numeric($args[1]) || intval($args[1]) <= 0) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cUsage: /is decrease <amount> <player>");
            return true;
        }

        $amount = intval($args[1]);
        $playerName = implode(" ", array_slice($args, 2));

        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);
        if ($player instanceof Player) {
            $player->sendMessage($this->plugin->NCDPrefix . "§aGiới hạn của hòn đảo của bạn đã được giảm xuống {$amount}");
        }

        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $name = strtolower($playerName);

        if (array_key_exists($name, $skyblockArray)) {
            $start = Position::fromObject($skyblockArray[$name]["Area"]["start"], $this->plugin->level);
            $end = Position::fromObject($skyblockArray[$name]["Area"]["end"], $this->plugin->level);

            $startX = $start->x + $amount;
            $startZ = $start->z + $amount; 
            $endX = $end->x - $amount;
            $endZ = $end->z - $amount; 

            if ($startX > $endX || $startZ > $endZ) {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cSố tiền bạn đã nhập lớn hơn đảo của {$name}. Chiến dịch bị Bỏ rơi.");
                return true;
            }

            $skyblockArray[$name]["Area"]["start"] = $start->add($amount, 0, $amount)->asVector3(); 
            $skyblockArray[$name]["Area"]["end"] = $end->subtract($amount, 0, $amount)->asVector3(); 

            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();
            $sender->sendMessage($this->plugin->NCDPrefix . "§aNgười chơi §f" . $name . " §agiới hạn đảo đã được giảm xuống {$amount}");
            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§aNgười chơi §f" . $name . " §akhông có đảo nào cả!");
            return true;
        }
    }
}
