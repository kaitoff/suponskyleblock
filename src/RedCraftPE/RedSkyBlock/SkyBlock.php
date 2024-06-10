<?php

namespace RedCraftPE\RedSkyBlock;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\block\tile\Tile;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use RedCraftPE\RedSkyBlock\Commands\Island;
use RedCraftPE\RedSkyBlock\Tasks\Generate;
use RedCraftPE\RedSkyBlock\Blocks\Lava;
use jojoe77777\FormAPI\{SimpleForm, CustomForm}; // Sửa FormAPI


class SkyBlock extends PluginBase implements Listener {
    
    public $NCDPrefix = "§l§6【§eSkyblock§6】 ";
    private $eventListener;
    private $island;
    public $playerList = [];
    
  	public function __construct(){
		self::$instance = $this;
	}
  public function onEnable(): void {
    $this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
    if (empty($this->cfg->get("SkyBlockWorld"))) {
      $this->getLogger()->info("§l§cSkyBlock §e↣ §aĐể plugin này hoạt động bình thường, bạn phải đặt thế giới SkyBlock trong máy chủ của mình. (In order for this plugin to function properly, you must set a SkyBlock world in your server).");
      $this->level = null;
    } else {
      $this->level = $this->getServer()->getWorldManager()->getWorldByName($this->cfg->get("SkyBlockWorld"));
      if (!$this->getServer()->getWorldManager()->isWorldLoaded($this->cfg->get("SkyBlockWorld"))) {
        if ($this->getServer()->getWorldManager()->loadWorld($this->cfg->get("SkyBlockWorld"))) {
          $this->level = $this->getServer()->getWorldManager()->getWorldByName($this->cfg->get("SkyBlockWorld"));
          $this->getLogger()->info("§l§cSkyBlock §e↣ §a SkyBlock is running on the world {$this->level->getFolderName()}");
        } else {
          $this->getLogger()->info("§l§cSkyBlock §e↣ §c The level currently set as the SkyBlock world does not exist.");
          $this->level = null;
        }
      } else {
        $this->getLogger()->info(TextFormat::GREEN . "SkyBlock is running on level {$this->level->getFolderName()}");
      }
    }
    $this->eventListener = new EventListener($this, $this->level);
    $this->getServer()->getPluginManager()->registerEvents($this->eventListener, $this);
    $this->island = new Island($this);
  }
 public function onLoad(): void {
    if (!is_dir($this->getDataFolder())) {
      @mkdir($this->getDataFolder());
    }
    $this->skyblock = new Config($this->getDataFolder() . "skyblock.yml", Config::YAML);
    $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);

    if (!$this->cfg->exists("PVP")) {
      $this->cfg->set("PVP", "off");
      $this->cfg->save();
    }

    $this->cfg->reload();
    $this->skyblock->reload();
  }
 public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
    return $this->island->onIslandCommand($sender, $command, $label, $args);
  }
  


 //API FUNCTIONS:
 public static function getInstance(): self {

  return self::$instance;
 }
 public function calcRank(string $name): string {

  $skyblockArray = $this->skyblock->get("SkyBlock", []);
  $users = [];

  if (!array_key_exists($name, $skyblockArray)) {

   return "N/A";
  }

  foreach(array_keys($skyblockArray) as $user) {

   $userValue = $skyblockArray[$user]["Value"];
   $users[$user] = $userValue;
  }

  arsort($users);
  $rank = array_search($name, array_keys($users)) + 1;

  return strval($rank);
 }
 public function getIslandName(Player $player): string {
    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());
    return $skyblockArray[$name]["Name"] ?? "N/A";
  }
 public function getMembers(Player $player): string {

  $skyblockArray = $this->skyblock->get("SkyBlock", []);
  $name = strtolower($player->getName());

  if (!array_key_exists($name, $skyblockArray)) {

   return "N/A";
  }

  return implode(", ", $skyblockArray[$name]["Members"]);
 }
 public function getValue(Player $player): string {

  $skyblockArray = $this->skyblock->get("SkyBlock", []);
  $name = strtolower($player->getName());

  if (!array_key_exists($name, $skyblockArray)) {

   return "N/A";
  }

  return strval($skyblockArray[$name]["Value"]);
 }
 public function getLockedStatus(Player $player): string {

  $skyblockArray = $this->skyblock->get("SkyBlock", []);
  $name = strtolower($player->getName());

  if (!array_key_exists($name, $skyblockArray)) {

   return "N/A";
  }

  if ($skyblockArray[$name]["Locked"]) {

   return "Yes";
  } else {

   return "No";
  }
 }
 public function getSize(Player $player): string {
    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {
      return "N/A";
    }

    $start = Position::fromObject($skyblockArray[$name]["Area"]["start"], $this->level); 
    $end = Position::fromObject($skyblockArray[$name]["Area"]["end"], $this->level); 
    $length = $end->x - $start->x; 
    $width = $end->z - $start->z; 

    return "{$length} x {$width}";
  }
	
	public function NCDMenuForm(Player $player, string $text, SkyBlock $plugin) { // thêm vào biến $plugin
		$form = new SimpleForm(function (Player $player, $data = null) use ($plugin) {
			$result = $data;
			if ($result === null) {
				return;
			}
			switch ($result) {
				case 0:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdcreate");
				break;
				case 1:
				$plugin->NCDWarpForm($player, "", $plugin); // Thêm $plugin
				break;
				case 2:
				$plugin->NCDSettingsForm($player, "", $plugin); // Thêm $plugin
				break;
				case 3:
				$plugin->NCDInfoForm($player, "", $plugin); // Thêm $plugin
				break;
				case 4:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdtop");
				break;
			}
		});
		$form->setTitle("§l§e༺ §cSkyBlock §e༻");
		$form->setContent($text."§l§c↣ §eXếp hạng đảo của bạn: §f" . $this->getValue($player));
		$form->addButton("§l§e• §cVào đảo §e•");
        $form->addButton("§l§e• §cĐến đảo người khác §e•");
        $form->addButton("§l§e• §cQuản lí đảo §e•");
        $form->addButton("§l§e• §cTra cứu đảo §e•");
        $form->addButton("§l§e• §cXếp hạng đảo §e•");
        $form->sendToPlayer($player);
        return $form;
    }

	
	public function NCDWarpForm(Player $player, string $text, SkyBlock $plugin)
    {
        $form = new CustomForm(function (Player $player, $data) use ($plugin) {
            $result = $data;
            if ($result === null) {
                $plugin->NCDMenuForm($player, "", $plugin); 
                return false;
            }
            if (empty($data[1])) {
                $plugin->NCDMenuForm($player, "§l§c↣ §cBạn chưa nhập tên người chơi.\n", $plugin); 
                return true;
            }
            $plugin->getServer()->getCommandMap()->dispatch($player, "is ncdtp " . $data[1]);
            return false;
        });
        $form->setTitle("§l§e༺ §cĐến đảo người khác §e༻");
        $form->addLabel($text);
        $form->addInput("§l§c↣ §aNhập tên người chơi", "§fNhập tên người chơi vào đây");
        $player->sendForm($form);
        return $form; // Trả về $form
    }


	
	public function NCDInfoForm(Player $player, string $text, SkyBlock $plugin)
    {
        $list = [];
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $list[] = $p->getName();
        }
        $this->playerList[$player->getName()] = $list;
        $form = new CustomForm(function(Player $player, $data) use ($plugin) { // Sử dụng use ($plugin)
            $result = $data;
            if ($result === null) {
                $plugin->NCDMenuForm($player, "", $plugin); // Truyền vào $plugin
                return false;
            }
            $index = $data[1];
            $playerName = $this->playerList[$player->getName()][$index];
            if ($playerName instanceof Player) {
            }
            $this->getServer()->getCommandMap()->dispatch($player, "is ncdinfo " . $playerName);
            return false;
        });
        $form->setTitle("§l§e༺ §cTra cứu đảo §e༻");
        $form->addLabel($text);
        $form->addDropdown("§l§c↣ §aChọn người chơi muốn tra cứu", $this->playerList[$player->getName()]);
        $form->sendToPlayer($player);
    }
	
	# Settings Form By Nguyễn Công Danh (NCD)
	public function NCDSettingsForm(Player $player) {
		$form = new SimpleForm(function (Player $player, ?int $data = null) {
			$result = $data;
			if ($result === null) {
				$this->NCDMenuForm($player, "");
				return;
			}
			switch ($result) {
				case 0:
				$this->NCDReNameForm($player, "");
				break;
				case 1:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdsetspawn");
				break;
				case 2:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdlock");
				break;
				case 3:
				$this->NCDAddRemoveForm($player, "");
				break;
				case 4:
				$this->NCDKickForm($player, "");
				break;
				case 5:
				$this->NCDBanUnBanForm($player, "");
				break;
				case 6:
				$this->NCDSettingSkyBlock($player, "");
				break;
			}
		});
		$form->setTitle("§l§e༺ §cSetting SkyBlock §e༻");
		$form->setContent("§l§c↣ §eMembers §f" . $this->getMembers($player));
		$form->addButton("§l§e• §cĐổi tên §e•");
		$form->addButton("§l§e• §cĐặt chỗ hồi sinh của đảo §e•");
		$form->addButton("§l§e• §cKhoá§e/§cMở khóa đảo §e•");
		$form->addButton("§l§e• §cThêm§e/§cXóa thành viên §e•");
		$form->addButton("§l§e• §cKick người chơi §e•");
		$form->addButton("§l§e• §cCấm§e/§cHủy cấm người chơi §e•");
		$form->addButton("§l§e• §cCài đặt đảo §e•");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function NCDReNameForm($player, string $text)
	{
		$form = new CustomForm(function(Player $player, $data) {
			$result = $data;
			if ($result === null) {
				$this->NCDSettingsForm($player);
				return false;
			}
			if (empty($data[1])) {
				$this->NCDSettingsForm($player);
				return true;
			}
			$this->getServer()->getCommandMap()->dispatch($player, "is ncdrename " . $data[1]);
			return false;
		});
		$form->setTitle("§l§e༺ §cĐổi tên đảo §༻");
		$form->addLabel($text);
		$form->addInput("§l§c↣ §aNhập tên", "§fNhập tên đảo mới vào đây");
		$form->sendToPlayer($player);
	}
	
	public function NCDAddRemoveForm($player, string $text)
	{
		$list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			$result = $data;
			if ($result === null) {
				$this->NCDSettingsForm($player);
				return false;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			switch ($data[2]) {
				case 0:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdadd " . $playerName);
				break;
				case 1:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdremove " . $playerName);
				break;
			}
			return false;
		});
		$form->setTitle("§l§e༺ §cThêm/Xóa thành viên §e༻");
		$form->addLabel($text."§l§c↣ §eThành viên §f" . $this->getMembers($player) . "\n");
		$form->addDropdown("§l§c↣ §aChọn người chơi", $this->playerList[$player->getName()]);
		$form->addToggle("§l§c↣ §aGạt sang để xóa");
		$form->sendToPlayer($player);
	}
	
	public function NCDKickForm($player, string $text)
	{
		$list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			$result = $data;
			if ($result === null) {
				$this->NCDSettingsForm($player);
				return false;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			$this->getServer()->getCommandMap()->dispatch($player, "is kick " . $playerName);
			return false;
		});
		$form->setTitle("§l§b♦ §cKick thành viên §b♦");
		$form->addLabel($text);
		$form->addDropdown("§l§c↣ §aChọn người chơi:", $this->playerList[$player->getName()]);
		$form->sendToPlayer($player);
	}
	
	public function NCDBanUnBanForm($player, string $text)
	{
		$list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			$result = $data;
			if ($result === null) {
				$this->NCDSettingsForm($player);
				return false;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			switch ($data[2]) {
				case 0:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdban " . $playerName);
				break;
				case 1:
				$this->getServer()->getCommandMap()->dispatch($player, "is ncdunban " . $playerName);
				break;
			}
			return false;
		});
		$form->setTitle("§l§b♦ §cCấm/Bỏ cấm thành viên §b♦");
		$form->addLabel($text."§l§c↣ §eDanh sách bị cấm: §f" . $this->getBanned($player) . "\n");
		$form->addDropdown("§l§c↣ §aChọn người chơi:", $this->playerList[$player->getName()]);
		$form->addToggle("§l§c↣ §aGạt sang để bỏ cấm");
		$form->sendToPlayer($player);
	}
	
	 public function NCDSettingSkyBlock(Player $player, string $text, SkyBlock $plugin) 
    {
        $form = new CustomForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                $this->NCDSettingsForm($player, "", $this);
                return false;
            }
			switch ($data[1]) {
				case 0:
				$name = strtolower($player->getName());
				$skyblockArray = $this->skyblock->get("SkyBlock", []);
				$skyblockArray[$name]["Settings"]["PVP"] = "off";
				$this->skyblock->set("SkyBlock", $skyblockArray);
				$this->skyblock->save();
				break;
				case 1:
				$name = strtolower($player->getName());
				$skyblockArray = $this->skyblock->get("SkyBlock", []);
				$skyblockArray[$name]["Settings"]["PVP"] = "on";
				$this->skyblock->set("SkyBlock", $skyblockArray);
				$this->skyblock->save();
				
				break;
			}
			switch ($data[2]) {
				case 0:
				$name = strtolower($player->getName());
				$skyblockArray = $this->skyblock->get("SkyBlock", []);
				$skyblockArray[$name]["Settings"]["Pickup"] = "off";
				$this->skyblock->set("SkyBlock", $skyblockArray);
				$this->skyblock->save();
				$this->NCDSettingSkyBlock($player, "§l§c↣ §aCài đặt đảo của bạn đã được cập nhật!\n", $this); 
				break;
				case 1:
				$name = strtolower($player->getName());
				$skyblockArray = $this->skyblock->get("SkyBlock", []);
				$skyblockArray[$name]["Settings"]["Pickup"] = "on";
				$this->skyblock->set("SkyBlock", $skyblockArray);
				$this->skyblock->save();
				$this->NCDSettingSkyBlock($player, "§l§c↣ §aCài đặt đảo của bạn đã được cập nhật!\n", $this);
				break;
			}
			return false;
		});
		$name = strtolower($player->getName());
		$form->setTitle("§l§b♦ §cCài đặt đảo §b♦");
		$form->addLabel($text."\n§l§c↣ §aGạt sang bên trái để tắt.\n");
		$skyblockArray = $this->skyblock->get("SkyBlock", []);
		if ($skyblockArray[$name]["Settings"]["PVP"] === "on") {
			$form->addToggle("§l§c↣ §eTắt/Bật PvP", true);
		} else {
			$form->addToggle("§l§c↣ §eTắt/Bật PvP", false);
		}
		if ($skyblockArray[$name]["Settings"]["Pickup"] === "on") {
			$form->addToggle("§l§c↣ §eTắt/Bật Pickup Protection", true);
		} else {
			$form->addToggle("§l§c↣ §eTắt/Bật Pickup Protection", false);
		}
		$form->sendToPlayer($player);
	}
}
