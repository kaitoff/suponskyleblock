<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
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

            $first = "N/A";
            $second = "N/A";
            $third = "N/A";
            $fourth = "N/A";
            $fifth = "N/A";

            usort($skyblockArray, function ($a, $b) {
                return $b['Value'] <=> $a['Value'];
            });

$topIslands = array_slice($skyblockArray, 0, 5);
            
            foreach ($topIslands as $index => $island) {
                if ($index === 0) {
                    $first = $island["Members"][0] ?? 'N/A';
                    $firstValue = $island["Value"];
                }
                if ($index === 1) {
                    $second = $island["Members"][0] ?? 'N/A';
                    $secondValue = $island["Value"];
                }
                if ($index === 2) {
                    $third = $island["Members"][0] ?? 'N/A';
                    $thirdValue = $island["Value"];
                }
                if ($index === 3) {
                    $fourth = $island["Members"][0] ?? 'N/A';
                    $fourthValue = $island["Value"];
                }
                if ($index === 4) {
                    $fifth = $island["Members"][0] ?? 'N/A';
                    $fifthValue = $island["Value"];
                }
            }
      $topInfoMessage = "§l§c↣ §aTop 5 Island SkyBlock\n\n" .
                "§l§c↣ §cTOP 1: §f{$first} §cđạt được: §e{$firstValue} §cđiểm\n\n" .
                "§l§c↣ §cTOP 2: §f{$second} §cđạt được: §e{$secondValue} §cđiểm\n\n" .
                "§l§c↣ §cTOP 3: §f{$third} §cđạt được: §e{$thirdValue} §cđiểm\n\n" .
                "§l§c↣ §cTOP 4: §f{$fourth} §cđạt được: §e{$fourthValue} §cđiểm\n\n" .
                "§l§c↣ §cTOP 5: §f{$fifth} §cđạt được: §e{$fifthValue} §cđiểm\n\n";
            
            if ($sender instanceof Player) {
                $this->plugin->NCDTopForm($sender, $topInfoMessage);
            } else {
                $sender->sendMessage($topInfoMessage);
            }

            return true;
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cBạn không có quyền để sử dụng lệnh này.");
            return true;
        }
    }
	
	# CODE FORM BY NGUYỄN CÔNG DANH (NCD)
public function NCDTopForm(Player $sender, string $text)
    {
        $form = new SimpleForm(function (Player $sender, ?int $data = null) {
            $result = $data;
            if ($result === null) {
                $this->plugin->NCDMenuForm($sender, "");
                return;
            }
            switch ($result) {
                case 0:
                    $this->plugin->NCDMenuForm($sender, "");
                    break;
            }
        });
        $form->setTitle("§l§b♦ §cXếp Hạng Đảo §b♦");
        $form->setContent($text);
        $form->addButton("§l§e• §cSubmit §e•");
        $form->sendToPlayer($sender);
        return $form;
    }
	