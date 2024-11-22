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
        $this->enabledCommands = $this->getConfig()->get("enabled_commands", []);
        foreach ($this->enabledCommands as $cmd) {
            if (!$this->isValidCommand($cmd)) {
                $this->getServer()->getLogger()->notice("Comando inválido na configuração enabled_commands (config.yml): ". $cmd);
            }
        }
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
                
                if (in_array($cmd, $this->enabledCommands)) {
                    switch ($cmd)
                    {
                        case "version":
                            $this->send($p, $botName . " §fEste servidor está usando a versão ". Main::PLUGIN_VERSION . " do plugin \"M4Bot\" :)");
                        break;
                        case "ship":
                            if (count($parts) == 2) {
                                $name1 = $parts[0];
                                $name2 = $parts[1];
                            
                                $this->send($p, str_replace(["{name1}", "{name2}", "{ship}"], [$name1, $name2, $this->ship($name1, $name2)], $botName . " " . $this->getMessages()->getNested("ship.success")));
                                $e->setCancelled(true);
                                return;
                            }
                        
                            $this->send($p, str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->getNested("ship.usage")));
                        break;
                        case "broxa":
                        case "brocha": // Fato aleatório: alguns dicionários dizem brocha e outros broxa
                            $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 100)], $botName . " " . $this->getMessages()->get("broxa")));
                        break;
                        case "lola":
                            $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 34)], $botName . " " . $this->getMessages()->get("lola")));
                        break;
                        case "calvo":
                            $this->send($p, str_replace(["{player}", "{value}"], [$p->getName(), rand(1, 100)], $botName . " " . $this->getMessages()->get("calvo")));
                        break;
                        case "help":
                            $p->sendMessage(str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->get("help")));
                        break;
                        case "ccme":
                            $p->sendMessage(str_replace(["{botname}", "{clear}"], [$botName, str_repeat("§f\n", 40)], $this->getMessages()->get("ccme")));
                        break;
                        case "piada":
                            $jokes = $this->getMessages()->getNested("piada.piadas", []);
                            if (empty($jokes)) {
                                $p->sendMessage($botName . " " . $this->getMessages()->getNested("piada.empty"));
                                $e->setCancelled(true);
                                return;
                            }
                            
                            $randomJoke = $jokes[array_rand($jokes)];
                            $p->sendMessage(str_replace("{piada}", $randomJoke, $botName . " " . $this->getMessages()->getNested("piada.success")));
                        break;
                        default:
                            $p->sendMessage(str_replace("{prefix}", $prefix, $botName . " " . $this->getMessages()->get("invalidcmd")));
                        break;
                    }
                } else {
                    $this->send($p, $botName . " " . $this->getMessages()->get("disabledcmd", "esse comando foi desativado..."));
                }
                $e->setCancelled(true);
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
    
    public function isValidCommand($cmd)
    {
        $validCommands = ["version", "ship", "broxa", "lola", "calvo", "help", "ccme", "piada"];
        return in_array($cmd, $validCommands);
    }
}
