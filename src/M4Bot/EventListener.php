<?php

namespace M4Bot;

use pocketmine\{Server, Player};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

use M4Bot\Main;

class EventListener implements Listener
{
    private $main;
    
    public function __construct(Main $main)
    {
        $this->main = $main;
    }
    
    public function getServer()
    {
        return $this->main->getServer();
    }
    
    public function getConfig()
    {
        return $this->main->getConfig();
    }

    public function getMessages()
    {
        return $this->main->getMessages();
    }
    
    public function onChat(PlayerChatEvent $e)
    {
        $p = $e->getPlayer();
        $msg = $e->getMessage();
        
        $prefix = $this->getConfig()->get("prefixo", "+");
        $botName = $this->getConfig()->get("nome", "§bM4§fBot");
        
        if (!$e->isCancelled()) {
            if (strpos($msg, $prefix) === 0) {
                $command = substr($msg, strlen($prefix));
                $parts = explode(" ", $command);
                $cmd = strtolower(array_shift($parts));
                
                switch ($cmd)
                {
                    case "version":
                        $this->send($p, $botName . " §fEste servidor está usando a versão 1 do plugin \"M4Bot\" :)");
                        $e->setCancelled(true);
                    break;
                    case "ship":
                        if (count($parts) == 2) {
                            $name1 = $parts[0];
                            $name2 = $parts[1];
                            
                            $this->send($p, str_replace(["{name1}", "{name2}", "{ship}"], [$name1, $name2, $this->ship($name1, $name2)], $botName . " " . $this->getMessages()->getNested("ship.sucess", "a chance de {name1} ficar com {name2} é {ship}")));
                            $e->setCancelled(true);
                            return;
                        }
                        
                        $this->send($p, str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->getNested("ship.usage", "por favor, utilize {prefix}ship (nome 1) (nome 2)")));
                        $e->setCancelled(true);
                    break;
                    case "broxa":
                    case "brocha": // Fato aleatório: alguns dicionários dizem brocha e outros broxa
                        $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 100)], $botName . " " . $this->getMessages()->get("broxa", "{player} está {value}% broxa")));
                        $e->setCancelled(true);
                    break;
                    case "lola":
                        $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 34)], $botName . " " . $this->getMessages()->get("lola", "a lola de {player} tem {value} cm")));
                        $e->setCancelled(true);
                    break;
                    case "calvo":
                        $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 100)], $botName . " " . $this->getMessages()->get("calvo", "{player} é {value}% calvo")));
                        $e->setCancelled(true);
                    break;
                    case "help":
                        $this->send($p, str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->getNested("help", "Comandos:\n{prefix}ship (nome1) (nome2)\n{prefix}broxa\n{prefix}lola\n{prefix}calvo")));
                        $e->setCancelled(true);
                    break;
                    case "ccme":
                        $this->send($p, str_replace(["{botname}", "{clear}"], [$botName, str_repeat("§f\n", 40)], $this->getMessages()->getNested("ccme", "{clear}{botname} chat limpo com sucesso")));
                        $e->setCancelled(true);
                    break;
                    default:
                        $this->send($p, str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->getNested("invalidcmd", "comando desconhecido, use {prefix}help")));
                        $e->setCancelled(true);
                    break;
                }
            }
        }
    }
    
    public function send(Player $p, string $msg)
    {
        $type = $this->getConfig()->get("tipo", "player");
        switch ($type)
        {
            case "player":
                $p->sendMessage($msg);
            break;
            case "server";
                $this->getServer()->broadcastMessage($msg);
            break;
            default:
                $p->sendMessage($msg);
                $this->getServer()->getLogger()->warning("A configuração \"tipo\" na config.yml está errada, usando \"player\" como padrão");
            break;
        }
    }
    
    public function ship(string $name1, string $name2)
    {
        $combinedName = strtolower($name1 . $name2);
        $shipScore = 0;

        for ($i = 0; $i < strlen($combinedName); $i++) {
            $shipScore += ord($combinedName[$i]) * ($i + 1);
        }

        $normalizedScore = $shipScore % 100;
        
        return $normalizedScore;
    }
}