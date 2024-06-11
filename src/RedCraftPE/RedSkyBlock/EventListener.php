<?php
namespace RedCraftPE\RedSkyBlock;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
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
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockFactory as BlockFactoryStatic;

use pocketmine\block\Block;
use pocketmine\world\Position;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Settings;

class EventListener implements Listener {

  private $plugin;

  private $level;

  private static $listener;

  public $fakeBlocks = [];

  public $fakeInvs = [];

  public function __construct($plugin, $level) {

    self::$listener = $this;
    $this->plugin = $plugin;
    $this->level = $level;
    $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
  }
  public static function getListener(): self {

    return self::$listener;
  }
  public function addFakeBlock(Block $block): bool {

    array_push($this->fakeBlocks, $block);
    return true;
  }
  public function addFakeInv(Inventory $inv): bool {

    array_push($this->fakeInvs, $inv);
    return true;
  }
public function onPlace(BlockPlaceEvent $event) {
    $player = $event->getPlayer();
    $block = $event->getBlockAgainst();
    $blockPosition = $block->getPosition();
    $level = $this->level;
    $plugin = $this->plugin;
    $valuableBlocks = $plugin->cfg->get("Valuable Blocks", []);

    if ($player->getWorld() === $level) {
        $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
        $blockX = $blockPosition->getX();
        $blockY = $blockPosition->getY();
        $blockZ = $blockPosition->getZ();
        $islandOwner = "";

        foreach ($skyblockArray as $skyblocks => $islandData) {
            $start = Position::fromObject(
                new Vector3($islandData["Area"]["start"]["X"], $islandData["Area"]["start"]["Y"], $islandData["Area"]["start"]["Z"]), 
                $this->level
            );
            $startX = $start->getX();
            $startY = $start->getY();
            $startZ = $start->getZ();
            $end = Position::fromObject(
                new Vector3($islandData["Area"]["end"]["X"], $islandData["Area"]["end"]["Y"], $islandData["Area"]["end"]["Z"]),
                $this->level
            );
            $endX = $end->getX();
            $endY = $end->getY();
            $endZ = $end->getZ();

            if ($blockX >= $startX && $blockY >= $startY && $blockZ >= $startZ && $blockX <= $endX && $blockY <= $endY && $blockZ <= $endZ) {
                $islandOwner = $skyblocks;
                break;
            }
        }

        if ($islandOwner === "") {
            if ($player->hasPermission("skyblock.bypass")) {
                return;
            }

            $event->cancel();
            $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot build here!");
            return;
        } else if (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {
            if (array_key_exists($block->getTypeId(), $valuableBlocks)) {
                $skyblockArray[$islandOwner]["Value"] += $valuableBlocks[$block->getTypeId()];
                $plugin->skyblock->set("SkyBlock", $skyblockArray);
                $plugin->skyblock->save();
            }
            return;
        } else {
            if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Build"] === "off") {
                return;
            }

            $event->cancel();
            $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot build here!");
            return;
        }
    }
}

public function onBreak(BlockBreakEvent $event) {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $blockPosition = $block->getPosition();
    $level = $this->level;
    $plugin = $this->plugin;
    $valuableBlocks = $plugin->cfg->get("Valuable Blocks", []);

    if ($player->getWorld() === $level) {
        $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
        $blockX = $blockPosition->getX();
        $blockY = $blockPosition->getY();
        $blockZ = $blockPosition->getZ();
        $islandOwner = "";

        foreach ($skyblockArray as $skyblocks => $islandData) {
            $startX = $islandData["Area"]["start"]["X"];
            $startY = $islandData["Area"]["start"]["Y"];
            $startZ = $islandData["Area"]["start"]["Z"];
            $endX = $islandData["Area"]["end"]["X"];
            $endY = $islandData["Area"]["end"]["Y"];
            $endZ = $islandData["Area"]["end"]["Z"];

            if ($blockX > $startX && $blockY > $startY && $blockZ > $startZ && $blockX < $endX && $blockY < $endY && $blockZ < $endZ) {
                $islandOwner = $skyblocks;
                break;
            }
        }

        if ($islandOwner === "") {
            if ($player->hasPermission("skyblock.bypass")) {
                return;
            }

            $event->cancel();
            $player->sendMessage("§l§cSkyBlock §e↣ §c You cannot break blocks here!");
            return;
        } elseif (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {
            $blockId = $block->getTypeId(); 
            if (array_key_exists($blockId, $valuableBlocks)) {
                $skyblockArray[$islandOwner]["Value"] -= $valuableBlocks[$blockId];
                $plugin->skyblock->set("SkyBlock", $skyblockArray);
                $plugin->skyblock->save();
            }
            return;
        } else {
            if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Break"] === "off") {
                return;
            }

            $event->cancel();
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
        $playerPos = $player->getPosition(); 

        if ($player->getWorld() === $level) {
            $plugin = $this->plugin;
            $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
            $playerX = $playerPos->getX(); 
            $playerY = $playerPos->getY(); 
            $playerZ = $playerPos->getZ();
            $islandOwner = "";

            foreach (array_keys($skyblockArray) as $skyblocks) {
                $startX = $skyblockArray[$skyblocks]["Area"]["start"]["X"];
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
	$level = $event->getPlayer()->getWorld()->getFolderName();
	
    $plugin = $this->plugin;
    $hunger = $plugin->cfg->get("Hunger");
    $void = $plugin->cfg->get("Void");
    $level = $this->level;

    if ($void === "off") {

      if ($player->getWorld() === $level) {

        if ($player->getY() < 0) {

          $player->teleport($player->getSpawn());
        }
      }
    }
    if ($hunger === "off") {

      if ($player->getWorld() === $level) {

        if ($player->getFood() < 20) {

          $player->setFood(20);
        }
      }
    }
  }
   public function onDeath(PlayerDeathEvent $event) {
        $event->setKeepInventory(true); 
    }

public function onTeleport(EntityTeleportEvent $event): void {
    $entity = $event->getEntity();
    if ($entity instanceof Player && !$event->isCancelled()) { 
        $from = $event->getFrom()->getWorld();
        $to = $event->getTo()->getWorld();
        $plugin = $this->plugin;

        if ($to->getFolderName() !== $plugin->cfg->get("SkyBlockWorld")) {
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
    $entityX = $entity->getX();
    $entityY = $entity->getY();
    $entityZ = $entity->getZ();
    $damager = $event->getDamager();
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
    $islandOwner = "";

    if ($entity instanceof Player && $damager instanceof Player) {

      if ($plugin->cfg->get("PVP") === "off") {

        if ($entity->getWorld()->getFolderName() === $plugin->cfg->get("SkyBlockWorld")) {

          $event->setCancelled();
        }
      } else {

        foreach (array_keys($skyblockArray) as $skyblocks) {

          $startX = $skyblockArray[$skyblocks]["Area"]["start"]["X"];
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
        } else {

          if ($skyblockArray[$islandOwner]["Settings"]["PVP"] === "on") {

            $event->setCancelled(true);
          }
        }
      }
    }
  }
public function onPickup(EntityItemPickupEvent $event) {
    $entity = $event->getEntity();
    $entityX = $entity->getPosition()->getX();
    $entityY = $entity->getPosition()->getY();
    $entityZ = $entity->getPosition()->getZ();
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);
    $islandOwner = "";

    if ($entity->getWorld()->getFolderName() === $plugin->cfg->get("SkyBlockWorld")) {
        foreach (array_keys($skyblockArray) as $skyblocks) {
            $startX = $skyblockArray[$skyblocks]["Area"]["start"]["X"];
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
    } else {
        $owningEntity = $entity->getOwningEntity();
        if ($owningEntity instanceof Player) {
            $ownerName = strtolower($owningEntity->getName());
            if ($ownerName === $islandOwner || in_array($ownerName, $skyblockArray[$islandOwner]["Members"])) {
                return; 
            } else {
                if ($skyblockArray[$islandOwner]["Settings"]["Pickup"] === "on") {
                    $event->cancel(); 
                }
            }
        }
    }
    }
}
 public function onInvClose(InventoryCloseEvent $event) {

    $inventory = $event->getInventory();
    $player = $event->getPlayer();
    $playerPos = $player->getPosition(); // Lấy vị trí của người chơi
    $xPos = (int) $playerPos->getX(); 
    $yPos = (int) $playerPos->getY();
    $zPos = (int) $playerPos->getZ(); 

    foreach ($this->fakeInvs as $inv) {
        if ($inventory === $inv) {
            $index = array_search($inv, $this->fakeInvs);
            unset($this->fakeInvs[$index]);
            foreach ($inventory->getViewers() as $viewer) {
                $viewer->sendMessage("§l§cSkyBlock §e↣ §c Island Settings Menu Closed.");
            }
        }
    }
    foreach ($this->fakeBlocks as $block) {
        if ($xPos === $block->x && $yPos + 3 === $block->y && $zPos === $block->z) {
            $newBlock = Block::get(Block::AIR);
            $newBlock->x = $block->x;
            $newBlock->y = $block->y;
            $newBlock->z = $block->z;
            $newBlock->level = $block->level;
            $newBlock->level->sendBlocks([$player], [$newBlock]);
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
    $item = null; // Khởi tạo $item

    foreach($actions as $action) {
        if ($action instanceof SlotChangeAction) {
            $sourceItem = $action->getSourceItem();
            if (!$sourceItem->isNull()) { 
                if($sourceItem instanceof ItemBlock){ 
                    $item = $sourceItem; 
                }
            }
        }
    }
    if ($item === null) { 
        return;
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
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
            $this->plugin->NCDMenuForm($player, "", $this->plugin);
          }
        }
      }
    }



  public function onJoin(PlayerJoinEvent $event) {

    $player = $event->getPlayer();
    $name = strtolower($player->getName());
    $plugin = $this->plugin;
    $skyblockArray = $plugin->skyblock->get("SkyBlock", []);

    if (array_key_exists($name, $skyblockArray)) {

      if (!$skyblockArray[$name]["Settings"]) {

        $skyblockArray[$name]["Settings"] = Array(
          "Build" => "on",
          "Break" => "on",
          "Pickup" => "on",
          "Anvil" => "on",
          "Chest" => "on",
          "CraftingTable" => "on",
          "Fly" => "on",
          "Hopper" => "on",
          "Brewing" => "on",
          "Beacon" => "on",
          "Buckets" => "on",
          "PVP" => "on",
          "FlintSteel" => "on",
          "Furnace" => "on",
          "EnderChest" => "on"
        );
        $plugin->skyblock->set("SkyBlock", $skyblockArray);
        $plugin->skyblock->save();
      }
    }
  }
	 
	 public function onBlockUpdate(BlockUpdateEvent $event)
{
    $block = $event->getBlock();
    $air = false;
    $stonecutter = false;
    for ($i = 0; $i < 6; $i++) {
        $nearBlock = $block->getSide($i);
        if ($nearBlock instanceof Air) {
            $air = true;
        } else if ($nearBlock->getTypeId() === BlockTypeIds::STONECUTTER) { 
            $stonecutter = true;
        }
        if ($air && $stonecutter) {
            $id = mt_rand(1, 20);

            $newBlock = match ($id) {
                2 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::COBBLESTONE),
                4 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::IRON_ORE),
                6 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::GOLD_ORE),
                8 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::EMERALD_ORE),
                10 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::COAL_ORE),
                12 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::REDSTONE_ORE),
                14 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::DIAMOND_ORE),
                16 => BlockFactoryStatic::getInstance()->get(BlockTypeIds::LAPIS_ORE),
                default => BlockFactoryStatic::getInstance()->get(BlockTypeIds::COBBLESTONE),
            };

            $block->getPosition()->getWorld()->setBlock($block->getPosition(), $newBlock, true, false);
            return;
        }
    }
}

}