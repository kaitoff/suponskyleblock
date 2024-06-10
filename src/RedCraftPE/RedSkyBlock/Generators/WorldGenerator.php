<?php

namespace RedCraftPE\RedSkyBlock\Generators;

use pocketmine\Server;
use pocketmine\world\generator\FlatGenerator;
use pocketmine\world\generator\GeneratorManager;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\CreateWorld;

class WorldGenerator {

    private static $instance;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        self::$instance = $this;
    }

    public function generateWorld(string $levelName) {
        $plugin = $this->plugin;
        $generator = GeneratorManager::getInstance()->fromGeneratorName(FlatGenerator::class);
        $options = $generator->getOptions();
        $options->preset = "3;minecraft:air;127;";

        $plugin->getServer()->getWorldManager()->generateLevel($levelName, $generator, $options);
    }
}
