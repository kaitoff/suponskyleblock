<?php

namespace RedCraftPE\RedSkyBlock\Commands;

use pocketmine\command\{Command, CommandSender};
use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\{Add, Ban, Create, CreateWorld, Custom, Decrease, Delete, Fly, Help, Hunger, Increase, Info, Kick, Lock, MakeSpawn, Members, Name, Pos1, Pos2, Rank, Reload, Remove, Reset, Set, SetSpawn, Settings, SetWorld, Teleport, Top, Unban, Unlock, VoidClass};

class Island
{

  private static $instance;
  private $plugin;
  private $add;
  private $ban;
  private $create;
  private $createWorld;
  private $custom;
  private $decrease;
  private $delete;
  private $fly;
  private $help;
  private $hunger;
  private $increase;
  private $info;
  private $kick;
  private $lock;
  private $makeSpawn;
  private $members;
  private $name;
  private $pos1;
  private $pos2;
  private $rank;
  private $reload;
  private $remove;
  private $reset;
  private $set;
  private $setSpawn;
  private $settings;
  private $setWorld;
  private $teleport;
  private $top;
  private $unban;
  private $unlock;
  private $void;

  public function __construct(SkyBlock $plugin) {
   $this->plugin = $plugin; 

       $this->add = new Add($plugin);
        $this->ban = new Ban($plugin);
        $this->create = new Create($plugin);
        $this->createWorld = new CreateWorld($plugin);
        $this->custom = new Custom($plugin);
        $this->decrease = new Decrease($plugin);
        $this->delete = new Delete($plugin);
        $this->fly = new Fly($plugin);
        $this->help = new Help($plugin);
        $this->hunger = new Hunger($plugin);
        $this->increase = new Increase($plugin);
        $this->info = new Info($plugin);
        $this->kick = new Kick($plugin);
        $this->lock = new Lock($plugin);
        $this->makeSpawn = new MakeSpawn($plugin);
        $this->members = new Members($plugin);
        $this->name = new Name($plugin);
        $this->pos1 = new Pos1($plugin);
        $this->pos2 = new Pos2($plugin);
        $this->rank = new Rank($plugin);
        $this->reload = new Reload($plugin);
        $this->remove = new Remove($plugin);
        $this->reset = new Reset($plugin);
        $this->set = new Set($plugin);
        $this->setSpawn = new SetSpawn($plugin);
        $this->settings = new Settings($plugin);
        $this->setWorld = new SetWorld($plugin);
        $this->teleport = new Teleport($plugin);
        $this->top = new Top($plugin);
        $this->unban = new Unban($plugin);
        $this->unlock = new Unlock($plugin);
        $this->void = new VoidClass($plugin);
  }
  public function onIslandCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender->hasPermission("skyblock.is")) {
            if (empty($args)) {
                    return $this->help->onHelpCommand($sender, $args);
            } else {
                switch (strtolower($args[0])) {
                    case "ncdadd":
                        return $this->add->onAddCommand($sender, $args);
                    case "ncdban":
                    case "ncdexpel":
                        return $this->ban->onBanCommand($sender, $args);
                    case "ncdcreate":
                            return $this->create->onCreateCommand($sender);
                    case "ncdcw":
                    case "ncdcreateworld":
                        return $this->createWorld->onCreateWorldCommand($sender, $args);
                    case "ncdcustom":
                        return $this->custom->onCustomCommand($sender, $args);
                    case "ncddecrease":
                        return $this->decrease->onDecreaseCommand($sender, $args);
                    case "ncddelete":
                        return $this->delete->onDeleteCommand($sender, $args);
                    case "ncdfly":
                        return $this->fly->onFlyCommand($sender);
                    case "ncdhelp":
                        return $this->help->onHelpCommand($sender, $args);
                    case "ncdhunger":
                        return $this->hunger->onHungerCommand($sender, $args);
                    case "ncdincrease":
                        return $this->increase->onIncreaseCommand($sender, $args);
                    case "ncdinfo":
                        return $this->info->onInfoCommand($sender, $args);
                    case "ncdkick":
                        return $this->kick->onKickCommand($sender, $args);
                    case "ncdclose":
                    case "ncdlock":
                        return $this->lock->onLockCommand($sender);
                    case "ncdmakespawn":
                    case "ncdcreatespawn":
                        return $this->makeSpawn->onMakeSpawnCommand($sender);
                    case "ncdmembers":
                        return $this->members->onMembersCommand($sender);
                    case "ncdrename":
                    case "ncdname":
                        return $this->name->onNameCommand($sender, $args);
                    case "ncdpos1":
                        return $this->pos1->onPos1Command($sender);
                    case "ncdpos2":
                        return $this->pos2->onPos2Command($sender);
                    case "ncdrank":
                        return $this->rank->onRankCommand($sender);
                    case "ncdload":
                    case "ncdreload":
                        return $this->reload->onReloadCommand($sender);
                    case "ncdremove":
                        return $this->remove->onRemoveCommand($sender, $args);
                    case "ncdrestart":
                    case "ncdreset":
                        return $this->reset->onResetCommand($sender);
                    case "ncdsw":
                    case "ncdsetworld":
                        return $this->setWorld->onSetWorldCommand($sender);
                    case "ncdsettings":
                        return $this->settings->onSettingsCommand($sender);
                    case "ncdset":
                        return $this->set->onSetCommand($sender);
                    case "ncdsetspawn":
                        return $this->setSpawn->onSetSpawnCommand($sender);
                    case "ncdspawn":
                    case "ncdgoto":
                    case "ncdgo":
                    case "ncdtp":
                    case "ncdteleport":
                    case "ncdvisit":
                        return $this->teleport->onTeleportCommand($sender, $args);
case "ncdlb":
case "ncdleaderboard":
case "ncdtop":
    return $this->top->showTopForm($sender, "§l§c↣ §aTop 5 Island SkyBlock\n\n" .
        "§l§c↣ §cTOP 1: §f{$first} §cđạt được: §e{$firstValue} §cđiểm\n\n" .
        "§l§c↣ §cTOP 2: §f{$second} §cđạt được: §e{$secondValue} §cđiểm\n\n" .
        "§l§c↣ §cTOP 3: §f{$third} §cđạt được: §e{$thirdValue} §cđiểm\n\n" .
        "§l§c↣ §cTOP 4: §f{$fourth} §cđạt được: §e{$fourthValue} §cđiểm\n\n" .
        "§l§c↣ §cTOP 5: §f{$fifth} §cđạt được: §e{$fifthValue} §cđiểm\n\n"); // Sửa thành showTopForm

                    case "ncdunban":
                    case "ncdpardon":
                        return $this->unban->onUnbanCommand($sender, $args);
                    case "ncdopen":
                    case "ncdunlock":
                        return $this->unlock->onUnlockCommand($sender);
                    case "ncdvoid":
                        return $this->void->onVoidCommand($sender, $args);
                    default:
                        return $this->help->onHelpCommand($sender, $args); 
                }
            }
       } else {
            $sender->sendMessage("§l§cSkyBlock §e↣ §cYou do not have the proper permissions to run this command.");
        }
        return true;
    }
}