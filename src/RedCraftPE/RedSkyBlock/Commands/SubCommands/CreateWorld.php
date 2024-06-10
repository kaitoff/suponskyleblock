<?php
namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Generators\WorldGenerator;

class CreateWorld {

    protected $NCDPrefix = "";

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->worldGenerator = new WorldGenerator($this->plugin);
	    $this->NCDPrefix = $plugin->NCDPrefix;
    }
  
    public function onCreateWorldCommand(CommandSender $sender, array $args): bool {
        if ($sender->hasPermission("skyblock.createworld")) {
            if (count($args) < 2) {
                $sender->sendMessage($this->NCDPrefix . "§cUsage: /is createworld <world name>");
                return true;
            }

            $world = (string) implode(" ", array_slice($args, 1));

            $levelManager = $this->plugin->getServer()->getWorldManager(); 

            if ($levelManager->isWorldLoaded($world)) {
                $sender->sendMessage($this->NCDPrefix . "§cThế giới bạn đang cố gắng tạo ra đã tồn tại.");
                return true;
            } 

            $this->worldGenerator->generateWorld($world);
            $this->plugin->cfg->set("SkyBlockWorld", $world);
            $this->plugin->cfg->save();
            $sender->sendMessage($this->NCDPrefix . "§aThế giới §f" . $world . " §ađã được tạo và đặt làm thế giới SkyBlock trong máy chủ này.");
            return true;

        } else {
            $sender->sendMessage($this->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
