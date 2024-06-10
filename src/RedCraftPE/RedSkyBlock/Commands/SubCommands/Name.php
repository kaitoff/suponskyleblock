<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\SkyBlock;

class Name
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onNameCommand(CommandSender $sender, array $args): bool
    {
        if ($sender->hasPermission("skyblock.name")) {
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            $senderName = strtolower($sender->getName());

            if (array_key_exists($senderName, $skyblockArray)) {
                $name = $skyblockArray[$senderName]["Name"] ?? ''; 

                if (count($args) < 2) {
                    $this->plugin->NCDReNameForm($sender, "§l§c↣ §aTên đảo của bạn là: §f" . $name . "§a.\n");
                } else {
                    $name = (string)implode(" ", array_slice($args, 1));
                    $skyblockArray[$senderName]["Name"] = $name;
                    $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
                    $this->plugin->skyblock->save();
                    $this->plugin->NCDReNameForm($sender, "§l§c↣ §aBạn đã đặt tên đảo thành: §f" . $name . "§a.\n");
                }
            } else {
                $this->plugin->NCDReNameForm($sender, "§l§c↣ §cBạn chưa có đảo nào cả.\n");
            }

            return true;
        }

        $sender->sendMessage("§l§cSkyBlock §e↣ §cYou do not have the proper permissions to run this command.");
        return true;
    }
}
