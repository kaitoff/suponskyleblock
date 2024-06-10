<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use RedCraftPE\RedSkyBlock\SkyBlock;
use jojoe77777\FormAPI\SimpleForm;

class Top
{
    protected $plugin;

    public function __construct(SkyBlock $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onTopCommand(CommandSender $sender): bool
    {
        if ($sender->hasPermission("skyblock.top")) {
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);

            // Sử dụng usort để sắp xếp mảng theo giá trị giảm dần
            usort($skyblockArray, function ($a, $b) {
                return $b['Value'] <=> $a['Value'];
            });

            // Lấy 5 hòn đảo đầu tiên
            $topIslands = array_slice($skyblockArray, 0, 5);

            $topInfoMessage = "§l§c↣ §aTop 5 Island SkyBlock\n\n";

            foreach ($topIslands as $index => $island) {
                $topInfoMessage .= "§l§c↣ §cTOP " . ($index + 1) . ": §f" . ($island["Members"][0] ?? 'N/A') . " §cđạt được: §e" . ($island["Value"] ?? 0) . " §cđiểm\n\n";
            }

            if ($sender instanceof Player) {
                $this->plugin->NCDTopForm($sender, $topInfoMessage, $this->plugin); // Truyền vào $this->plugin
            } else {
                $sender->sendMessage($topInfoMessage);
            }

            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }

    // CODE FORM BY NGUYỄN CÔNG DANH (NCD)
   public function showTopForm(Player $sender, string $text) {
        $form = new SimpleForm(function (Player $sender, ?int $data = null) use ($plugin) { // Sử dụng use ($plugin)
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $plugin->NCDMenuForm($sender, "", $plugin); // Truyền vào $plugin
                    break;
            }
        });
        $form->setTitle("§l§b♦ §cXếp Hạng Đảo §b♦");
        $form->setContent($text);
        $form->addButton("§l§e• §cSubmit §e•");
        $form->sendToPlayer($sender);
        return $form;
    }
}
