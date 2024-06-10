<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\block\{BlockFactory, Block, BlockLegacyIds as BlockIds};
use RedCraftPE\RedSkyBlock\SkyBlock;

class Set {
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function onSetCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.set")) {
            $skyblock = $this->plugin->skyblock;
            $x1 = $skyblock->get("x1");
            $x2 = $skyblock->get("x2");
            $y1 = $skyblock->get("y1");
            $y2 = $skyblock->get("y2");
            $z1 = $skyblock->get("z1");
            $z2 = $skyblock->get("z2");
            $level = $sender->getWorld();
            $blocksArray = [];

            if ($skyblock->get("Pos1") && $skyblock->get("Pos2")) {
                for ($x = min($x1, $x2); $x <= max($x1, $x2); $x++) {
                    for ($y = min($y1, $y2); $y <= max($y1, $y2); $y++) {
                        for ($z = min($z1, $z2); $z <= max($z1, $z2); $z++) {
                            $block = $level->getWorld()->getBlockAt($x, $y, $z);

                            $blockID = $block->getId();
                            $blockMeta = $block->getMeta();

                            if (in_array($blockID, [BlockIds::LEAVES, BlockIds::LEAVES2])) {
                                $noDecayMeta = [0, 4, 8, 9, 10, 11, 12, 13, 14, 15];
                                if (!in_array($blockMeta, $noDecayMeta)) {
                                    $blockDamage = 8; // Giả sử chuyển tất cả các loại lá thành Oak Leaves (không mục nát)
                                }
                            }

                            array_push($blocksArray, $blockID . " " . $blockMeta);
                        }
                    }
                }

                $skyblock->set("Blocks", $blocksArray);
                $skyblock->set("Custom", true);
                $skyblock->save();
                $sender->sendMessage($this->plugin->NCDPrefix . "§aYour new SkyBlock custom island has been set!");
                return true;
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cYou must set the custom island position 1 and position 2 before using this command!");
                return true;
            }
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
