<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\world\Position;
use pocketmine\world\World;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate;

class Reset
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onResetCommand(CommandSender $sender): bool
    {
        if (!$sender->hasPermission("skyblock.reset")) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }

        $interval = $this->plugin->cfg->get("Interval");
        $itemsArray = $this->plugin->cfg->get("Starting Items", []);
        $levelName = $this->plugin->cfg->get("SkyBlockWorld");
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $islands = $this->plugin->skyblock->get("Islands");
        $initialSize = $this->plugin->cfg->get("Island Size");
        $senderName = strtolower($sender->getName());

        if ($levelName === "") {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou must set a SkyBlock world in order for this plugin to function properly.");
            return true;
        }

        $level = $this->plugin->getServer()->getWorldManager()->getWorldByName($levelName);
        if ($level === null && !$this->plugin->getServer()->getWorldManager()->loadWorld($levelName)) {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cThe world currently set as the SkyBlock world does not exist.");
            return true;
        }

        if (array_key_exists($senderName, $skyblockArray)) {
            unset($skyblockArray[$senderName]);
            $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
            $this->plugin->skyblock->save();

            if ($sender instanceof Player) {
                $sender->getInventory()->clearAll();
                $sender->sendMessage($this->plugin->NCDPrefix . "§aYour island has been completely reset.");

                if ($this->plugin->skyblock->get("Custom")) {
                    $customX = $this->plugin->skyblock->get("CustomX") ?? 0; 
                    $customY = $this->plugin->skyblock->get("CustomY") ?? 0;
                    $customZ = $this->plugin->skyblock->get("CustomZ") ?? 0;
                    $sender->teleport(new Position($islands * $interval + $customX, 15 + $customY, $islands * $interval + $customZ, $level));
                } else {
                    $sender->teleport(new Position($islands * $interval + 2, 15 + 3, $islands * $interval + 4, $level));
                }

                $sender->setGamemode(GameMode::SPECTATOR()); 
                $this->plugin->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);

                foreach ($itemsArray as $items) {
                    if (count($itemsArray) > 0) {
                        $itemArray = explode(" ", $items);
                        if (count($itemArray) === 3) {
                            $id = intval($itemArray[0]);
                            $damage = intval($itemArray[1]);
                            $count = intval($itemArray[2]);
                            $sender->getInventory()->addItem(VanillaItems::get($id, $damage, $count));
                        }
                    }
                }
                $senderPosition = $sender->getPosition();
                $skyblockArray[$senderName] = [
                    "Name" => $sender->getName() . "'s Island",
                    
                ];
                $this->plugin->skyblock->set("SkyBlock", $skyblockArray);
                $this->plugin->skyblock->save();
            }
            return true; 
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have an island yet.");
            return true;
        }
    }
}
