<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{
    command\CommandSender, command\PluginCommand, plugin\Plugin, Server, Player, utils\TextFormat as C
};

use ErosionYT\TPA\TPA;

class TPACommand extends PluginCommand{

    public function __construct(TPA $owner){
        parent::__construct("tpa", $owner);
        $this->setDescription($this->getPlugin()->getConfig()->get("tpaCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if(isset($args[0])){
                if(($target = $this->getPlugin()->getServer()->getPlayer($args[0])) !== null){
                    $this->getPlugin()->sendTPARequest($target, $player);

                    $player->sendMessage("§6» §7You sent a teleport request successfully");
                }else{
                    $player->sendMessage("§cThat player cannot be found");
                }
            }
        }
        return true;
    }
}