<?php

namespace RedCraftPE\RedSkyBlock\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\world\World;
use pocketmine\player\Player;

use RedCraftPE\RedSkyBlock\Generator\IslandGenerator; // Cập nhật namespace

class Generate extends Task {

    private IslandGenerator $generator;
    private int $islands;
    private World $level; 
    private int $interval;
    private Player $sender;

    public function __construct(int $islands, World $level, int $interval, Player $sender) {
        $this->islands = $islands;
        $this->level = $level; 
        $this->interval = $interval;
        $this->sender = $sender;

        $this->generator = new IslandGenerator();
    }

    public function onRun(int $tick): void {
        $this->generator->generateIsland($this->level->getWorld(), $this->interval, $this->islands); 
    }
}
