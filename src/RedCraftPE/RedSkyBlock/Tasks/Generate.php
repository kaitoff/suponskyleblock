<?php

namespace RedCraftPE\RedSkyBlock\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\world\World; 
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\Generators\IslandGenerator;

class Generate extends Task {

    private $generator;
    private $islands;
    private $level; 
    private $interval;
    private $sender;

    public function __construct(int $islands, World $level, int $interval, Player $sender) {
        $this->islands = $islands;
        $this->level = $level;
        $this->interval = $interval;
        $this->sender = $sender;

        $this->generator = new IslandGenerator();
    }

    public function onRun(): void { 
        $this->generator->generateIsland($this->level, $this->interval, $this->islands);
        $this->sender->setImmobile(false);
    }
}
