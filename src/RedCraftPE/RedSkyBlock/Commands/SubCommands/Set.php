<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
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
                            $blockDamage = $block->getMeta(); 

              if ($blockID === BlockFactory::get(Block::LEAVES)->getID() || $blockID === BlockFactory::get(Block::LEAVES2)->getID()) {

                $oakNoDecay = [0, 4, 12];
                $spruceNoDecay = [1, 5, 13];
                $birchNoDecay = [2, 6, 14];
                $jungleNoDecay = [3, 7, 15];
                $acaciaNoDecay = [0, 4, 12];
                $darkNoDecay = [1, 5, 13];

                if (in_array($blockDamage, $oakNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 8;
                if (in_array($blockDamage, $spruceNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 9;
                if (in_array($blockDamage, $birchNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 10;
                if (in_array($blockDamage, $jungleNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 11;
                if (in_array($blockDamage, $acaciaNoDecay) && $blockID === BlockFactory::get(Block::LEAVES2)->getID()) $blockDamage = 8;
                if (in_array($blockDamage, $darkNoDecay) && $blockID === BlockFactory::get(Block::LEAVES2)->getID()) $blockDamage = 9;
              }

              array_push($blocksArray, $blockID . " " . $blockDamage);
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
