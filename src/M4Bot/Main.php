<?php

namespace M4Bot;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use M4Bot\EventListener;

class Main extends PluginBase
{
    public function onEnable()
    {
        $this->getServer()->getLogger()->info(">==> M4Bot <==<");
        $this->getServer()->getLogger()->info("> By M4theuskkj");
        $this->getServer()->getLogger()->info("> Carregando config.yml, messages.yml & iniciando listener");

        $this->initFiles();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function initFiles()
    {
        $path = $this->getDataFolder();
        
        if (!is_dir($path)) {
            @mkdir($path);
        }
        
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        
        $this->config = new Config($path . "config.yml", Config::YAML);
        $this->messages = new Config($path . "messages.yml", Config::YAML);
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
}
