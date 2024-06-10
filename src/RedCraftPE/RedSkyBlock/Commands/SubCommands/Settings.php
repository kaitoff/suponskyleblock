<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\nbt\NetworkLittleEndianNBTStream;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\EventListener;
use RedCraftPE\RedSkyBlock\Commands\Island;

class Settings {

    protected $plugin;
  
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
  public function onSettingsCommand(CommandSender $sender): bool {
        if ($sender->hasPermission("skyblock.island.settings")) {
            $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
            if (array_key_exists(strtolower($sender->getName()), $skyblockArray)) {
                $this->createSettingsInventory($sender);
                $sender->sendMessage($this->plugin->NCDPrefix . "§aIsland Settings Menu Opened.");
                return true;
            } else {
                $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have an island yet.");
                return true;
            }
        } else {
            $sender->sendMessage($this->plugin->NCDPrefix . "§cYou do not have the proper permissions to run this command.");
            return true;
        }
    }
  public function createSettingsInventory(Player $player) {
        $skyblockArray = $this->plugin->skyblock->get("SkyBlock", []);
        $name = strtolower($player->getName());

        $position = $player->getPosition();
        $blockPos = $position->add(0, 3, 0)->asVector3(); // Sử dụng Position để tính toán tọa độ

        // Đặt block trước
        $block = VanillaBlocks::CHEST();
        $block->setPosition($blockPos);
        $player->getWorld()->setBlock($blockPos, $block); // Sử dụng setBlock()

        $player->getWorld()->sendBlocks([$player], [$block]); // Gửi cập nhật block

        $nbt = CompoundTag::create()
            ->setString(Tile::TAG_ID, Tile::CHEST)
            ->setInt(Tile::TAG_X, $blockPos->x)
            ->setInt(Tile::TAG_Y, $blockPos->y)
            ->setInt(Tile::TAG_Z, $blockPos->z)
            ->setListTag(Tile::TAG_ITEMS, []);

        $inv = new FakeInventory();
        EventListener::getListener()->addFakeInv($inv);
        
$items = [
            0 => VanillaItems::COBBLESTONE()->setCustomName("Build Protection")->setLore([$skyblockArray[$name]["Settings"]["Build"]]),
            2 => VanillaItems::DIAMOND_PICKAXE()->setCustomName("Break Protection")->setLore([$skyblockArray[$name]["Settings"]["Break"]]),
            4 => VanillaItems::GUNPOWDER()->setCustomName("Pickup Protection")->setLore([$skyblockArray[$name]["Settings"]["Pickup"]]),
6 => VanillaItems::ANVIL()->setCustomName("Anvil Protection")->setLore([$skyblockArray[$name]["Settings"]["Anvil"]]),
8 => VanillaItems::CHEST()->setCustomName("Chest Protection")->setLore([$skyblockArray[$name]["Settings"]["Chest"]]),
9 => VanillaItems::CRAFTING_TABLE()->setCustomName("Crafting Table")->setLore([$skyblockArray[$name]["Settings"]["CraftingTable"]]),
11 => VanillaItems::ELYTRA()->setCustomName("Flying")->setLore([$skyblockArray[$name]["Settings"]["Fly"]]),
13 => VanillaItems::HOPPER()->setCustomName("Hopper Protection")->setLore([$skyblockArray[$name]["Settings"]["Hopper"]]),
15 => VanillaItems::BREWING_STAND()->setCustomName("Brewing")->setLore([$skyblockArray[$name]["Settings"]["Brewing"]]),
17 => VanillaItems::BEACON()->setCustomName("Beacon Protection")->setLore([$skyblockArray[$name]["Settings"]["Beacon"]]),
18 => VanillaItems::BUCKET()->setCustomName("Buckets")->setLore([$skyblockArray[$name]["Settings"]["Buckets"]]),
20 => VanillaItems::DIAMOND_SWORD()->setCustomName("PVP Protection")->setLore([$skyblockArray[$name]["Settings"]["PVP"]]),
22 => VanillaItems::FLINT_AND_STEEL()->setCustomName("Flint and Steel")->setLore([$skyblockArray[$name]["Settings"]["FlintAndSteel"]]),
24 => VanillaItems::FURNACE()->setCustomName("Furnace Protection")->setLore([$skyblockArray[$name]["Settings"]["Furnace"]]),
26 => VanillaItems::ENDER_CHEST()->setCustomName("Ender Chest")->setLore([$skyblockArray[$name]["Settings"]["EnderChest"]])

];
 foreach ($items as $slot => $item) {
            $inv->setItem($slot, $item);
        }
        $player->addWindow($inv);
        $packet = UpdateBlockPacket::create(
            $blockPos->x, $blockPos->y, $blockPos->z,
            UpdateBlockPacket::FLAG_NONE,
            VanillaBlocks::CHEST()->getFullId()
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        $pk = new UpdateBlockPacket();
        $pk->blockPosition = $blockPos;
        $pk->flags = UpdateBlockPacket::FLAG_NETWORK;
        $pk->dataLayer = 0;
        $pk->newData = $block->getFullId();
        $player->getNetworkSession()->sendDataPacket($pk);
        EventListener::getListener()->addFakeBlock($block);
    }
}
