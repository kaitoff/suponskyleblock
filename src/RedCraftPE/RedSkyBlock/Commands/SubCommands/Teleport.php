<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\world\Position;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Teleport {
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  
    public function onTeleportCommand(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("skyblock.create")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }

        $levelName = $this->plugin->cfg->get("SkyBlockWorld");
        if (empty($levelName)) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou must set a SkyBlock world in order for this plugin to function properly.");
            return true;
        }

        $levelManager = $this->plugin->getServer()->getWorldManager(); 

        $level = $levelManager->getWorldByName($levelName);
        if ($level === null && !$levelManager->loadWorld($levelName)) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cThe world currently set as the SkyBlock world does not exist.");
            return true;
        }

        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $senderName = strtolower($sender->getName());

        if (count($args) < 2) {
            if (array_key_exists($senderName, $skyblockArray)) {
                $spawnPosition = Position::fromObject($skyblockArray[$senderName]["Spawn"], $level);
                $sender->teleport($spawnPosition);
                $sender->sendMessage($this->plugin->NCDPrefix . "§aChào mừng đến với hòn đảo của bạn.");
            } else {
                $this->plugin->getServer()->getCommandMap()->dispatch($sender, "is ncdcreate");
            }

            return true;
        } else {
           if ($sender->hasPermission("skyblock.tp")) {
                $name = strtolower(implode(" ", array_slice($args, 1)));

                if (array_key_exists($name, $skyblockArray)) {
                    if ($skyblockArray[$name]["Locked"] === false || in_array($sender->getName(), $skyblockArray[$name]["Members"])) {
                        if (!in_array($sender->getName(), $skyblockArray[$name]["Banned"])) {
                            $spawnPosition = Position::fromObject($skyblockArray[$name]["Spawn"], $level);
                            $sender->teleport($spawnPosition);
                            $sender->setFlying(false);
                            $sender->setAllowFlight(false);
                            $sender->sendMessage($this->plugin->NCDPrefix . "§aChào mừng đến với hòn đảo của §f{$skyblockArray[$name]["Name"]}§a.");
                        } else {
                            $this->plugin->NCDWarpForm($sender, "§l§c↣ §f" . $skyblockArray[$name]["Members"][0] . " §cđã cấm bạn vào đảo của họ.\n\n");
                        }
                    } else {
                        $this->plugin->NCDWarpForm($sender, "§l§c↣ §f" . $skyblockArray[$name]["Members"][0] . "'§cs is locked.\n\n");
                    }
                } else {
                    $this->plugin->NCDWarpForm($sender, "§l§c↣ §f" . implode(" ", array_slice($args, 1)) . " §ckhông có đảo nào cả.\n\n");
                }
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            }
            return true;
        }
    }
}
