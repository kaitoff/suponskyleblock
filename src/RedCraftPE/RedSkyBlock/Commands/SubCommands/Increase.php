<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Increase {

    protected $plugin;
    protected $NCDPrefix = "";

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
	    $this->NCDPrefix = $plugin->NCDPrefix;
    }

    public function onIncreaseCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.size")) {
            $sender->sendMessage($this->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        if (count($args) < 3 || !is_numeric($args[1]) || intval($args[1]) <= 0) {
            $sender->sendMessage($this->NCDPrefix . "§cUsage: /is increase <amount> <player>");
            return true;
        }

        $amount = intval($args[1]);
        $playerName = implode(" ", array_slice($args, 2));

        $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);
        if ($player instanceof Player) {
            $player->sendMessage($this->NCDPrefix . "§aGiới hạn của hòn đảo của bạn đã được tăng lên §f{$amount} §ablock.");
        }

        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $name = strtolower($playerName);

        if (array_key_exists($name, $skyblockArray)) {
            $start = Position::fromObject($skyblockArray[$name]["Area"]["start"], $this->plugin->level);
            $end = Position::fromObject($skyblockArray[$name]["Area"]["end"], $this->plugin->level);

            $startX = $start->x - $amount;
            $startZ = $start->z - $amount; 
            $endX = $end->x + $amount;
            $endZ = $end->z + $amount; 

            $skyblockArray[$name]["Area"]["start"] = $start->subtract($amount, 0, $amount)->asVector3();
            $skyblockArray[$name]["Area"]["end"] = $end->add($amount, 0, $amount)->asVector3(); 

            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();
            $sender->sendMessage($this->NCDPrefix . "§aNgười chơi §f" . $name . " §agiới hạn đảo đã được tăng lên §f{$amount}§a.");
            return true;
        } else {
            $sender->sendMessage($this->NCDPrefix . "§cNgười chơi §f" . $name . " §cchưa có đảo nào cả.");
            return true;
        }
    }
}
