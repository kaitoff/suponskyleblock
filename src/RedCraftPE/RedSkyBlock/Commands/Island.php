<?php

namespace RedCraftPE\RedSkyBlock\Commands;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Add;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Ban;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Create;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\CreateWorld;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Custom;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Decrease;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Delete;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Fly;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Help;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Hunger;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Increase;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Info;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Kick;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Lock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\MakeSpawn;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Members;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Name;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Pos1;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Pos2;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Rank;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Reload;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Remove;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Reset;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Set;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\SetSpawn;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Settings;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\SetWorld;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Teleport;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Top;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Unban;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Unlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\VoidClass;

class Island {

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
  public function onIslandCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
    if ($sender->hasPermission("skyblock.is")) {
      if (empty($args)) {
        return $this->help->onHelpCommand($sender, $args);
      } else {
        switch (strtolower($args[0])) {
          case "ncdadd":
            return $this->add->onAddCommand($sender, $args);
          break;
          case "ncdban":
          case "ncdexpel":

            return $this->ban->onBanCommand($sender, $args);
          break;
          case "ncdcreate":

            return $this->create->onCreateCommand($sender);
          break;
          case "ncdcw":
          case "ncdcreateworld":

            return $this->createWorld->onCreateWorldCommand($sender, $args);
          break;
          case "ncdcustom":

            return $this->custom->onCustomCommand($sender, $args);
          break;
          case "ncddecrease":

            return $this->decrease->onDecreaseCommand($sender, $args);
          break;
          case "ncddelete":

            return $this->delete->onDeleteCommand($sender, $args);
          break;
          case "ncdfly":

            return $this->fly->onFlyCommand($sender);
          break;
          case "ncdhelp":

            return $this->help->onHelpCommand($sender, $args);
          break;
          case "ncdhunger":

            return $this->hunger->onHungerCommand($sender, $args);
          break;
          case "ncdincrease":

            return $this->increase->onIncreaseCommand($sender, $args);
          break;
          case "ncdinfo":

            return $this->info->onInfoCommand($sender, $args);
          break;
          case "ncdkick":

            return $this->kick->onKickCommand($sender, $args);
          break;
          case "ncdclose":
          case "ncdlock":

            return $this->lock->onLockCommand($sender);
          break;
          case "ncdmakespawn":
          case "ncdcreatespawn":

            return $this->makeSpawn->onMakeSpawnCommand($sender);
          break;
          case "ncdmembers":

            return $this->members->onMembersCommand($sender);
          break;
          case "ncdrename":
          case "ncdname":

            return $this->name->onNameCommand($sender, $args);
          break;
          case "ncdpos1":

            return $this->pos1->onPos1Command($sender);
          break;
          case "ncdpos2":

            return $this->pos2->onPos2Command($sender);
          break;
          case "ncdrank":

            return $this->rank->onRankCommand($sender);
          break;
          case "ncdload":
          case "ncdreload":

            return $this->reload->onReloadCommand($sender);
          break;
          case "ncdremove":

            return $this->remove->onRemoveCommand($sender, $args);
          break;
          case "ncdrestart":
          case "ncdreset":

            return $this->reset->onResetCommand($sender);
          break;
          case "ncdsw":
          case "ncdsetworld":

            return $this->setWorld->onSetWorldCommand($sender);
          break;
          case "ncdsettings":

            return $this->settings->onSettingsCommand($sender);
          break;
          case "ncdset":

            return $this->set->onSetCommand($sender);
          break;
          case "ncdsetspawn":

            return $this->setSpawn->onSetSpawnCommand($sender);
          break;
          case "ncdspawn":
          case "ncdgoto":
          case "ncdgo":
          case "ncdtp":
          case "ncdteleport":
          case "ncdvisit":

            return $this->teleport->onTeleportCommand($sender, $args);
          break;
          case "ncdlb":
          case "ncdleaderboard":
          case "ncdtop":

            return $this->top->onTopCommand($sender);
          break;
          case "ncdunban":
          case "ncdpardon":

            return $this->unban->onUnbanCommand($sender, $args);
          break;
          case "ncdopen":
          case "ncdunlock":

            return $this->unlock->onUnlockCommand($sender);
          break;
          case "ncdvoid":
            return $this->void->onVoidCommand($sender, $args);
        }
        return $this->help->onHelpCommand($sender, $args);
      }
    } else {
      $sender->sendMessage("§l§cSkyBlock §e↣ §cYou do not have the proper permissions to run this command.");
    }
    return true; 
  }
}
