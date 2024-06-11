<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RedCraftPE\RedSkyBlock\SkyBlock;
use pocketmine\world\WorldManager;

class Teleport {
	
    private $plugin;
    protected $NCDPrefix = "";

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
        $this->NCDPrefix = $plugin->NCDPrefix; // Use the prefix from SkyBlock
    }

    public function onTeleportCommand(CommandSender $sender, array $args): bool {
        if ($sender->hasPermission("skyblock.create")) {
            $levelName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld");
            if (empty($levelName)) {
                $sender->sendMessage($this->NCDPrefix . "§cVui lòng thiết lập tên thế giới SkyBlock trong config.yml.");
                return true;
            } 

            $worldManager = $this->plugin->getServer()->getWorldManager();
            $level = $worldManager->getWorldByName($levelName); // Lấy world thông qua WorldManager

            if ($level === null) {
                $sender->sendMessage($this->NCDPrefix . "§cThế giới SkyBlock không tồn tại hoặc chưa được tải.");
                return true;
            } 

            $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
            $senderName = strtolower($sender->getName());
            
            // Kiểm tra xem thế giới Skyblock đã được tải hay chưa
            if (!$worldManager->isWorldLoaded($levelName)) {
                if (!$worldManager->loadWorld($levelName)) {
                    $sender->sendMessage($this->NCDPrefix . "§cThế giới hiện được đặt là thế giới SkyBlock không tồn tại.");
                    return true;
                } else {
                    $level = $worldManager->getWorldByName($levelName);
                }
            }

            if (count($args) < 2) {
                if (array_key_exists($senderName, $skyblockArray)) {
                    $x = $skyblockArray[$senderName]["Area"]["start"]["X"];
                    $z = $skyblockArray[$senderName]["Area"]["start"]["Z"];
                    $sender->teleport(new Position($skyblockArray[$senderName]["Spawn"]["X"], $skyblockArray[$senderName]["Spawn"]["Y"], $skyblockArray[$senderName]["Spawn"]["Z"], $level));
                    $sender->sendMessage($this->NCDPrefix . "§aChào mừng đến với hòn đảo của bạn.");
                    return true;
                } else {
                    SkyBlock::getInstance()->getServer()->getCommandMap()->dispatch($sender, "is ncdcreate");
                    return true;
                }
            } else {
                if ($sender->hasPermission("skyblock.tp")) {
                    $name = strtolower(implode(" ", array_slice($args, 1)));

                    if (array_key_exists($name, $skyblockArray)) {
                        if ($skyblockArray[$name]["Locked"] === false || in_array($sender->getName(), $skyblockArray[$name]["Members"])) {
                            if (!in_array($sender->getName(), $skyblockArray[$name]["Banned"])) {
                                $x = $skyblockArray[$name]["Area"]["start"]["X"];
                                $z = $skyblockArray[$name]["Area"]["start"]["Z"];

                                $sender->teleport(new Position($skyblockArray[$name]["Spawn"]["X"], $skyblockArray[$name]["Spawn"]["Y"], $skyblockArray[$name]["Spawn"]["Z"], $level));
                                $sender->setFlying(false);
                                $sender->setAllowFlight(false);
                                $sender->sendMessage($this->NCDPrefix . "§aChào mừng đến với hòn đảo của §f{$skyblockArray[$name]["Name"]}§a.");
                                return true;
                            } else {
                                SkyBlock::getInstance()->NCDWarpForm($sender, "§l§c↣ §f" . $skyblockArray[$name]["Members"][0] . " §cđã cấm bạn vào đảo của họ.\n\n");
                                return true;
                            }
                        } else {
                            SkyBlock::getInstance()->NCDWarpForm($sender, "§l§c↣ §f" . $skyblockArray[$name]["Members"][0] . "'§cs is locked.\n\n");
                            return true;
                        }
                    } else {
                        SkyBlock::getInstance()->NCDWarpForm($sender, "§l§c↣ §f" . implode(" ", array_slice($args, 1)) . " §ckhông có đảo nào cả.\n\n");
                        return true;
                    }
                } else {
                    $sender->sendMessage($this->NCDPrefix."§cYou do not have the proper permissions to run this command.");
                    return true;
                }
            }
        } else {
            $sender->sendMessage($this->NCDPrefix."§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
}
