<?php

namespace RedCraftPE\RedSkyBlock\Generators;

use pocketmine\world\World;
use pocketmine\block\{Block, BlockFactory, BlockLegacyIds as BlockIds};

use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\level\generator\object\Tree;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Create;

class IslandGenerator {

  public static $instance;

  public function __construct() {

    self::$instance = $this;
  }
  public function generateIsland(World $level, $interval, $islands) {
        if (SkyBlock::getInstance()->skyblock->get("Custom") === false) {
            for ($x = $islands * $interval; $x < ($islands * $interval) + 3; $x++) {
                for ($y = 15; $y < 18; $y++) {
                    for ($z = $islands * $interval; $z < ($islands * $interval) + 6; $z++) {
                        $blockType = ($y < 17) ? BlockLegacyIds::STONE : BlockLegacyIds::GRASS; 
                        $level->getWorld()->setBlock(new Vector3($x, $y, $z), BlockFactory::getInstance()->get($blockType)); 
                        if ($x === ($islands * $interval) + 1 && $z === $islands * $interval && $y === 17) {
                            $worldGenerator = new WorldGenerator($plugin);
                            $worldGenerator->generateTreeAt($level, new Vector3($x, $y + 1, $z));
                        }
                    }
                }
            }
      for ($x = ($islands * $interval) - 2; $x < $islands * $interval; $x++) {

        for ($y = 15; $y < 18; $y++) {

          for ($z = ($islands * $interval) + 3; $z < ($islands * $interval) + 6; $z++) {

            if ($y < 17) {

             $level->setBlock(new Vector3($x, $y, $z), BlockFactory::getInstance()->get(Block::STONE));
            } else {

            $level->setBlock(new Vector3($x, $y, $z), BlockFactory::getInstance()->get(Block::GRASS));
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
                        $level->getWorld()->setBlock(new Vector3($x, $y, $z), BlockFactory::getInstance()->get($block[0], $block[1]), false);
                        $counter++;
                    }
                }
            }
        }
    }
}
