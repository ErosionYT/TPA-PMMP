<?php

namespace ErosionYT\TPA;

use ErosionYT\TPA\commands\TPAcceptCommand;
use ErosionYT\TPA\commands\TPACommand;
use ErosionYT\TPA\commands\TPADenyCommand;
use ErosionYT\TPA\commands\TPAHereCommand;
use pocketmine\{
    plugin\PluginBase, player\Player, utils\Config, utils\TextFormat as C
};

class TPA extends PluginBase{

    /** @var Config $config */
    protected $config;

    /** @var string[] $tpaReq  */
    public $tpaReq = [];

    public function onEnable(): void{
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "time" => "SECONDS",
            "tpaExpireTime" => 30,
            "tpacceptCommandDescription" => "Accept a teleport request",
            "tpaCommandDescription" => "Send a teleport request to the target player to teleport to them",
            "tpahereCommandDescription" => "Send a teleport request to the target player to teleport to you",
            "tpadenyCommandDescription" => "Deny a teleport request",
        ]);

        $this->initCommands();
    }

    public function onDisable() : void{
        $this->config->save();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config{
        return $this->config;
    }
    /**
     * @param Player $target
     * @param Player $requester
     */
    public function sendTPARequest(Player $target, Player $requester): void{
        $this->tpaReq[$target->getName()] = ["time" => time(), "teleportee" => $requester->getName()];
        $target->sendMessage(
            "§6» §7" . $requester->getName() . " wants to teleport to you, /tpaccept or /tpadeny");
    }

    /**
     * @param Player $target
     * @param Player $requester
     */
    public function sendTPAHereRequest(Player $target, Player $requester): void{
        $this->tpaReq[$target->getName()] = ["time" => time(), "teleportee" => $target->getName(), "teleport" => $requester->getName()];
        $target->sendMessage(
            "§6» §7" . $requester->getName() . " wants you to teleport to them, /tpaccept or /tpadeny");
    }

    /**
     * @param Player $player
     */
    public function denyTPARequest(Player $player): void{
        unset($this->tpaReq[$player->getName()]);

        if($this->teleporteeStillOnline($player)){
            $this->getTeleportee($player)->sendMessage(C::RED . $player->getName() . " has denied your request");
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasRequest(Player $player): bool{
        $this->updateRequest($player);

        return isset($this->tpaReq[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function teleporteeStillOnline(Player $player): bool{
        if(isset($this->tpaReq[$player->getName()])){
            if($this->getTeleportee($player) == null){
                $this->updateRequest($player);
                unset($this->tpaReq[$player->getName()]);
            }
            return $this->getTeleportee($player) !== null;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return null|Player
     */
    public function getTeleportee(Player $player): ?Player{
        if(isset($this->tpaReq[$player->getName()])){
            if(isset($this->tpaReq[$player->getName()]["teleport"])){
                return $this->getServer()->getPlayerByPrefix($this->tpaReq[$player->getName()]["teleport"]);
            }else{
                return $this->getServer()->getPlayerByPrefix($this->tpaReq[$player->getName()]["teleportee"]);
            }
        }
        return null;
    }

    /**
     * @param Player $player
     */
    public function destroyRequest(Player $player): void{
        unset($this->tpaReq[$player->getName()]);
    }

    /**
     * @param Player $player
     */
    private function updateRequest(Player $player): void{
        if(isset($this->tpaReq[$player->getName()])){
            if($this->tpaReq[$player->getName()]["time"] - $this->getConfig()->get("tpaExpireTime") <= time()){
                if(($teleportee = $this->getTeleportee($player)) !== null){
                    $teleportee->sendMessage("§6» §7Your §6TELEPORT §7request has expired");
                }
                $player->sendMessage("§6» §7Your §6TELEPORT §7request has expired");
                unset($this->tpaReq[$player->getName()]);
                return;
            }
        }
    }

    private function initCommands(): void{
        $commands = [
            new TPACommand($this),
            new TPAHereCommand($this),
            new TPAcceptCommand($this),
            new TPADenyCommand($this)
        ];

        $this->getServer()->getCommandMap()->registerAll("ErosionYT", $commands);
    }
}