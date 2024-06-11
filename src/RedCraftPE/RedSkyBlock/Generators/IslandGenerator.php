<?php

namespace RedCraftPE\RedSkyBlock\Generators;

use pocketmine\block\BlockTypeIds; // Thêm namespace BlockTypeIds
use pocketmine\block\BlockFactory as BlockFactoryStatic;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\generator\object\Tree;

use RedCraftPE\RedSkyBlock\SkyBlock;

class IslandGenerator {

    public static $instance;

    public function __construct() {
        self::$instance = $this;
    }

    public function generateIsland($level, $interval, $islands) {
        if (SkyBlock::getInstance()->skyblock->get("Custom") === false) {

            for ($x = $islands * $interval; $x < ($islands * $interval) + 3; $x++) {
                for ($y = 15; $y < 18; $y++) {
                    for ($z = $islands * $interval; $z < ($islands * $interval) + 6; $z++) {
                        if ($y < 17) {
                            $level->setBlock(new Vector3($x, $y, $z), BlockFactoryStatic::getInstance()->get(BlockTypeIds::STONE)); // Thay đổi ở đây
                        } else {
                            $level->setBlock(new Vector3($x, $y, $z), BlockFactoryStatic::getInstance()->get(BlockTypeIds::GRASS)); // Thay đổi ở đây
                        }
                        if ($x === ($islands * $interval) + 1 && $z === $islands * $interval && $y === 17) {
                            Tree::growTree($level, $x, $y + 1, $z, new Random(), 0);
                        }
                    }
                }
            }
            
            for ($x = ($islands * $interval) - 2; $x < $islands * $interval; $x++) {
                for ($y = 15; $y < 18; $y++) {
                    for ($z = ($islands * $interval) + 3; $z < ($islands * $interval) + 6; $z++) {
                        if ($y < 17) {
                            $level->setBlock(new Vector3($x, $y, $z), BlockFactoryStatic::getInstance()->get(BlockTypeIds::STONE)); // Thay đổi ở đây
                        } else {
                            $level->setBlock(new Vector3($x, $y, $z), BlockFactoryStatic::getInstance()->get(BlockTypeIds::GRASS)); // Thay đổi ở đây
                        }
                    }
                }
            }
        } else {
            $x1 = SkyBlock::getInstance()->skyblock->get("x1");
            $x2 = SkyBlock::getInstance()->skyblock->get("x2");
            $y1 = SkyBlock::getInstance()->skyblock->get("y1");
            $y2 = SkyBlock::getInstance()->skyblock->get("y2");
            $z1 = SkyBlock::getInstance()->skyblock->get("z1");
            $z2 = SkyBlock::getInstance()->skyblock->get("z2");
            $blocksArray = SkyBlock::getInstance()->skyblock->get("Blocks", []);
            $counter = 0;

            for ($x = $islands * $interval; $x <= ($islands * $interval) + (max($x1, $x2) - min($x1, $x2)); $x++) {
                for ($y = 15; $y <= 15 + (max($y1, $y2) - min($y1, $y2)); $y++) {
                    for ($z = $islands * $interval; $z <= ($islands * $interval) + (max($z1, $z2) - min($z1, $z2)); $z++) {
                        $block = explode(" ", $blocksArray[$counter]);
                        $level->setBlock(new Vector3($x, $y, $z), BlockFactoryStatic::getInstance()->get(intval($block[0]), intval($block[1])), false); // Sử dụng BlockFactoryStatic::getInstance()->get(...)
                        $counter++;
                    }
                }
            }
        }
    }
}
