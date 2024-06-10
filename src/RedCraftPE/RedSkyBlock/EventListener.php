<?php

namespace RedCraftPE\RedSkyBlock;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\block\Block;
use pocketmine\world\Position;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Settings;

class EventListener implements Listener
{
    private $plugin;
    private $level;
    private static $listener;
    public $fakeBlocks = [];
    public $fakeInvs = [];
    public $playerList = [];

    public function __construct($plugin, $level)
    {
        self::$listener = $this;
        $this->plugin = $plugin;
        $this->level = $level;
    }

    public static function getListener(): self
    {
        return self::$listener;
    }

    public function addFakeBlock(Block $block): bool
    {
        array_push($this->fakeBlocks, $block);
        return true;
    }

    public function addFakeInv(Inventory $inv): bool
    {
        array_push($this->fakeInvs, $inv);
        return true;
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $level = $this->level;
        $plugin = $this->plugin;
        $valuableBlocks = $plugin->cfg->get("Valuable Blocks", []);

        if ($player->getWorld() === $level) {
            $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
            $blockX = $block->getPosition()->getX();
            $blockY = $block->getPosition()->getY();
            $blockZ = $block->getPosition()->getZ();
            $islandOwner = "";

            foreach (array_keys($skyblockArray) as $skyblocks) {
                $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
                $startX = $start->getX();
                $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
                $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
                $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
                $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
                $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

                if ($blockX >= $startX && $blockY >= $startY && $blockZ >= $startZ && $blockX <= $endX && $blockY <= $endY && $blockZ <= $endZ) {
                    $islandOwner = $skyblocks;
                    break;
                }
            }
            if ($islandOwner === "") {
                if ($player->hasPermission("skyblock.bypass")) {
                    return;
                }
                $event->cancel(); // Sửa thành cancel()
                $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot build here!");
                return;
            } else if (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {
                if (array_key_exists($block->getId(), $valuableBlocks)) { // Sửa thành getId()
                    $skyblockArray[$islandOwner]["Value"] += $valuableBlocks[$block->getId()];
                    $plugin->skyblock->set("SkyBlock", $skyblockArray);
                    $plugin->skyblock->save();
                }
                return;
            } else {
                if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Build"] === "off") {
                    return;
                }
                $event->cancel(); // Sửa thành cancel()
                $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot build here!");
                return;
            }
        }
    }
  public function onBreak(BlockBreakEvent $event) {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $level = $this->level;
    $plugin = $this->plugin;
    $valuableBlocks = $plugin->cfg->get("Valuable Blocks", []);

    if ($player->getWorld() === $level) { // Sử dụng getWorld()
      $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
      $blockX = $block->getPosition()->getX(); // Sử dụng getPosition()
      $blockY = $block->getPosition()->getY(); // Sử dụng getPosition()
      $blockZ = $block->getPosition()->getZ(); // Sử dụng getPosition()
      $islandOwner = "";

      foreach (array_keys($skyblockArray) as $skyblocks) {
        $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
        $startX = $start->getX();
        $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
        $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
        $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
        $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
        $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

        if ($blockX >= $startX && $blockY >= $startY && $blockZ >= $startZ && $blockX < $endX && $blockY < $endY && $blockZ < $endZ) {
          $islandOwner = $skyblocks;
          break;
        }
      }
      if ($islandOwner === "") {
        if ($player->hasPermission("skyblock.bypass")) {
          return;
        }

        $event->cancel(); // Sửa thành cancel()
        $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot break blocks here!");
        return;
      } elseif (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {

        if (array_key_exists($block->getId(), $valuableBlocks)) { // Sửa thành getId()

          $skyblockArray[$islandOwner]["Value"] -= $valuableBlocks[$block->getId()];
          $plugin->skyblock->set("SkyBlock", $skyblockArray);
          $plugin->skyblock->save();
        }
        return;
      } else {

        if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Break"] === "off") {

          return;
        }

        $event->cancel(); // Sửa thành cancel()
        $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot break blocks here!");
        return;
      }
    }
  }
  public function onInteract(PlayerInteractEvent $event) {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $item = $event->getItem();
    $level = $this->level;

    if ($player->hasPermission("skyblock.bypass")) {
      return;
    }

    $blockId = $block->getId(); // Sử dụng getId()
    $itemId = $item->getId(); // Sử dụng getId()

    if ($blockId === 54 || $blockId === 61 || $blockId === 62 || $block->getID() === 138 || $block->getID() === 130 || $item->getID() === 259 || $block->getID() === 145 || $block->getID() === 58 || $block->getID() === 154 || $block->getID() === 117) {

      if ($player->getWorld() === $level) {

        $plugin = $this->plugin;
        $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
        $playerX = $player->getX();
        $playerY = $player->getY();
        $playerZ = $player->getZ();
        $islandOwner = "";

        foreach (array_keys($skyblockArray) as $skyblocks) {

          $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
$startX = $start->x;
          $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
          $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
          $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
          $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
          $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

          if ($playerX > $startX && $playerY > $startY && $playerZ > $startZ && $playerX < $endX && $playerY < $endY && $playerZ < $endZ) {

            $islandOwner = $skyblocks;
            break;
          }
        }
        if ($islandOwner === "") {

          $event->setCancelled(true);
          $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot use this here!");
          return;
        } elseif (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {

          return;
        } else {

          if ($block->getID() === 54 && $skyblockArray[$islandOwner]["Settings"]["Chest"] === "off") return;
          if (($block->getID() === 61 || $block->getID() === 62) && $skyblockArray[$islandOwner]["Settings"]["Furnace"] === "off") return;
          if ($block->getID() === 138 && $skyblockArray[$islandOwner]["Settings"]["Beacon"] === "off") return;
          if ($block->getID() === 130 && $skyblockArray[$islandOwner]["Settings"]["EnderChest"] === "off") return;
          if ($block->getID() === 259 && $skyblockArray[$islandOwner]["Settings"]["FlintAndSteel"] === "on") return;
          if ($block->getID() === 145 && $skyblockArray[$islandOwner]["Settings"]["Anvil"] === "off") return;
          if ($block->getID() === 58 && $skyblockArray[$islandOwner]["Settings"]["CraftingTable"] === "on") return;
          if ($block->getID() === 154 && $skyblockArray[$islandOwner]["Settings"]["Hopper"] === "off") return;
          if ($block->getID() === 117 && $skyblockArray[$islandOwner]["Settings"]["Brewing"] === "on") return;

          $event->setCancelled(true);
          $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot use this here!");
          return;
        }
      }
    }
  }
  public function onBucketEvent(PlayerBucketEvent $event) {

    $player = $event->getPlayer();
    $level = $this->level;

    if ($player->getWorld() === $level) {

      $plugin = $this->plugin;
      $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
      $playerX = $player->getX();
      $playerY = $player->getY();
      $playerZ = $player->getZ();
      $islandOwner = "";

      foreach (array_keys($skyblockArray) as $skyblocks) {

        $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
$startX = $start->x;
        $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
        $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
        $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
        $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
        $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

        if ($playerX > $startX && $playerY > $startY && $playerZ > $startZ && $playerX < $endX && $playerY < $endY && $playerZ < $endZ) {

          $islandOwner = $skyblocks;
          break;
        }
      }
      if ($islandOwner === "") {

        if ($player->hasPermission("skyblock.bypass")) {

          return;
        }

        $event->setCancelled(true);
        $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot use this here!");
        return;
      } else if (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {

        return;
      } else {

        if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Buckets"] === "on") {

          return;
        }

        $event->setCancelled(true);
        $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot use this here!");
        return;
      }
    }
  }
   public function onMove(PlayerMoveEvent $event) {
    $player = $event->getPlayer();
    $plugin = $this->plugin;
    $hunger = $plugin->cfg->get("Hunger");
    $void = $plugin->cfg->get("Void");
    $level = $this->level;

    if ($void === "off" && $player->getWorld() === $level && $player->getPosition()->getY() < 0) {
      $player->teleport($this->plugin->getServer()->getWorldManager()->getDefaultWorld()?->getSafeSpawn()); // Sửa lại teleport
    }
    if ($hunger === "off" && $player->getWorld() === $level && $player->getHungerManager()->getFood() < 20) {
      $player->getHungerManager()->setFood(20);
    }
  }

  public function onDeath(PlayerDeathEvent $event) {
    $event->setKeepInventory(true);
  }
 public function onEntityTeleport(EntityTeleportEvent $event){ 
    $entity = $event->getEntity();
    $target = $event->getTo();
    $plugin = $this->plugin;

    if ($entity instanceof Player) {
      if ($target->getWorld()->getFolderName() !== $plugin->cfg->get("SkyBlockWorld")) {
        if ($entity->getAllowFlight()) {
          if ($entity->getGamemode() !== 1) {
            if ($entity->isFlying()) {
              $entity->setFlying(false);
            }
            $entity->setAllowFlight(false);
          }
        }
      }
    }
  }

 public function onDamage(EntityDamageByEntityEvent $event) {
    $entity = $event->getEntity();
    $damager = $event->getDamager();
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
    $islandOwner = "";
    if ($entity instanceof Player && $damager instanceof Player) {
      if ($plugin->cfg->get("PVP") === "off") {
        if ($entity->getWorld()->getFolderName() === $plugin->cfg->get("SkyBlockWorld")) { 
          $event->cancel(); 
        }
      } else {
        foreach (array_keys($skyblockArray) as $skyblocks) {
          $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
          $end = Position::fromObject($skyblockArray[$skyblocks]["Area"]["end"], $this->level);

          if ($entity->getPosition()->asVector3()->x > $start->x && $entity->getPosition()->asVector3()->y > $start->y && $entity->getPosition()->asVector3()->z > $start->z && 
              $entity->getPosition()->asVector3()->x < $end->x && $entity->getPosition()->asVector3()->y < $end->y && $entity->getPosition()->asVector3()->z < $end->z) {
            $islandOwner = $skyblocks;
            break;
          }
        }
        if ($islandOwner !== "" && $skyblockArray[$islandOwner]["Settings"]["PVP"] === "on") {
          $event->cancel();
        }
      }
    }
  }
  public function onPickup(InventoryPickupItemEvent $event) {

    $viewers = $event->getViewers();
    $entity;
    foreach($viewers as $key => $viewer) {

      $entity = $viewer;
    }
    $entityX = $entity->getX();
    $entityY = $entity->getY();
    $entityZ = $entity->getZ();
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
    $islandOwner = "";

    if ($entity instanceof Player) {

      if ($entity->getLevel()->getFolderName() === $plugin->cfg->get("SkyBlockWorld")) {

        foreach (array_keys($skyblockArray) as $skyblocks) {

          $start = Position::fromObject($skyblockArray[$skyblocks]["Area"]["start"], $this->level);
$startX = $start->x;
          $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
          $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
          $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
          $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
          $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

          if ($entityX > $startX && $entityY > $startY && $entityZ > $startZ && $entityX < $endX && $entityY < $endY && $entityZ < $endZ) {

            $islandOwner = $skyblocks;
            break;
          }
        }
        if ($islandOwner === "") {

          return;
        } else if (in_array($entity->getName(), $skyblockArray[$islandOwner]["Members"])) {

          return;
        } else {

          if ($skyblockArray[$islandOwner]["Settings"]["Pickup"] === "on") {

            $event->setCancelled(true);
          }
        }
      }
    }
  }
public function onInvClose(InventoryCloseEvent $event) {

    $inventory = $event->getInventory();
    $player = $event->getPlayer();
    $xPos = (int) $player->getX();
    $yPos = (int) $player->getY();
    $zPos = (int) $player->getZ();

    foreach($this->fakeInvs as $inv) {

      if ($inventory === $inv) {

        $index = array_search($inv, $this->fakeInvs);
        unset($this->fakeInvs[$index]);
        foreach($inventory->getViewers() as $viewer) {

          $viewer->sendMessage("§l§cSkyBlock §e↣ §c Island Settings Menu Closed.");
        }
      }
    }
    foreach($this->fakeBlocks as $block) {
      if ($xPos === $block->getPosition()->getX() && $yPos + 3 === $block->getPosition()->getY() && $zPos === $block->getPosition()->getZ()) {
        $newBlock = VanillaBlocks::AIR();
        $block->getWorld()->setBlock($block->getPosition(), $newBlock);
        $index = array_search($block, $this->fakeBlocks);
        unset($this->fakeBlocks[$index]);
      }
    }
}
  public function onInventoryTransaction(InventoryTransactionEvent $event) {

    $transaction = $event->getTransaction();
    $inventories = $transaction->getInventories();
    $player = $transaction->getSource();
    $name = strtolower($player->getName());
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
    $actions = $transaction->getActions();
    $item;

    foreach($actions as $action) {

      if ($action instanceof SlotChangeAction) {

        if ($action->getSourceItem()->getID() !== 0) {

          $item = $action->getSourceItem();
        }
      }
    }

   foreach ($inventories as $inventory) {
    if (in_array($inventory, $this->fakeInvs) && isset($item)) { 
        $event->cancel();
      if ($item->getId() === VanillaItems::COBBLESTONE()->getId()) { 

            if ($skyblockArray[$name]["Settings"]["Build"] === "on") {

              $skyblockArray[$name]["Settings"]["Build"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §a Build protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Build"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage(TextFormat::GREEN . "Build protection has been enabled on your island.");
            }
            $inventory->close($player);
} else if ($item->getId() === VanillaItems::DIAMOND_PICKAXE()->getId()) { 

            if ($skyblockArray[$name]["Settings"]["Break"] === "on") {

              $skyblockArray[$name]["Settings"]["Break"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aBreak protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Break"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aBreak protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getId() === VanillaItems::GUNPOWDER()->getId()) {

            if ($skyblockArray[$name]["Settings"]["Pickup"] === "on") {

              $skyblockArray[$name]["Settings"]["Pickup"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aPickup protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Pickup"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aPickup protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getId() === VanillaItems::ANVIL()->getId()) {

            if ($skyblockArray[$name]["Settings"]["Anvil"] === "on") {

              $skyblockArray[$name]["Settings"]["Anvil"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aAnvil protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Anvil"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aAnvil protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::CHEST)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Chest"] === "on") {

              $skyblockArray[$name]["Settings"]["Chest"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aChest protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Chest"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aChest protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::CRAFTING_TABLE)->getID()) {

            if ($skyblockArray[$name]["Settings"]["CraftingTable"] === "on") {

              $skyblockArray[$name]["Settings"]["CraftingTable"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can no longer use crafting tables on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["CraftingTable"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can now use crafting tables on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::ELYTRA)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Fly"] === "on") {

              $skyblockArray[$name]["Settings"]["Fly"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can no longer fly on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Fly"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can now fly on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::HOPPER)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Hopper"] === "on") {

              $skyblockArray[$name]["Settings"]["Hopper"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aHopper protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Hopper"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aHopper protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::BREWING_STAND_BLOCK)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Brewing"] === "on") {

              $skyblockArray[$name]["Settings"]["Brewing"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can no longer brew potions on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Brewing"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can now brew potions on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::BEACON)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Beacon"] === "on") {

              $skyblockArray[$name]["Settings"]["Beacon"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aBeacon protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Beacon"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aBeacon protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::BUCKET)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Buckets"] === "on") {

              $skyblockArray[$name]["Settings"]["Buckets"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can no longer use buckets on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Buckets"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can now use buckets on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::DIAMOND_SWORD)->getID()) {

            if ($skyblockArray[$name]["Settings"]["PVP"] === "on") {

              $skyblockArray[$name]["Settings"]["PVP"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aPVP protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["PVP"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aPVP protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::FLINT_STEEL)->getID()) {

            if ($skyblockArray[$name]["Settings"]["FlintAndSteel"] === "on") {

              $skyblockArray[$name]["Settings"]["FlintAndSteel"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can no longer use flint and steel on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["FlintAndSteel"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aOther players can now use flint and steel on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::FURNACE)->getID()) {

            if ($skyblockArray[$name]["Settings"]["Furnace"] === "on") {

              $skyblockArray[$name]["Settings"]["Furnace"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aFurnace protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["Furnace"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aFurnace protection has been enabled on your island.");
            }
            $inventory->close($player);
          } else if ($item->getID() === Item::get(Item::ENDER_CHEST)->getID()) {

            if ($skyblockArray[$name]["Settings"]["EnderChest"] === "on") {

              $skyblockArray[$name]["Settings"]["EnderChest"] = "off";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aEnder chest protection has been disabled on your island.");
            } else {

              $skyblockArray[$name]["Settings"]["EnderChest"] = "on";
              $plugin->skyblock->set("SkyBlock", $skyblockArray);
              $plugin->skyblock->save();
              $player->sendMessage("§l§cSkyBlock §e↣ §aEnder chest protection has been enabled on your island.");
            }
            $inventory->close($player);
          }
        }
      }
    }
  }
   public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$this->plugin->NCDMenuForm($player, "", $this->plugin);
	}
	 public function onQuit(PlayerQuitEvent $ev){
		$player = $ev->getPlayer();
		unset($this->plugin->playerList[$player->getName()]);
	}
	 public function onBlockUpdate(BlockUpdateEvent $event)
	{
		$block = $event->getBlock();
		$air = false;
		$stonecutter = false;
		for ($i = 0; $i <= 1; $i++) {
			$nearBlock = $block->getSide($i);
			if ($nearBlock instanceof Air) {
				$air = true;
				} else if ($nearBlock instanceof Stonecutter) {
					$stonecutter = true;
				}
				if ($air && $stonecutter) {
					$id = mt_rand(1, 20);

                switch ($id) {

                    case 2;

                        $newBlock = new Cobblestone();

                        break;

                    case 4;

                        $newBlock = new IronOre();

                        break;

                    case 6;

                        $newBlock = new GoldOre();

                        break;

                    case 8;

                        $newBlock = new EmeraldOre();

                        break;

                    case 10;

                        $newBlock = new CoalOre();

                        break;

                    case 12;

                        $newBlock = new RedstoneOre();

                        break;

                    case 14;

                        $newBlock = new DiamondOre();

                        break;

                    case 16;

                        $newBlock = new LapisOre();

                        break;

                    default:

                        $newBlock = new Cobblestone();
					}
					$block->getLevel()->setBlock($block, $newBlock, true, false);
					return;
			}
		}
	}
}
