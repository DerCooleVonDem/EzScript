<?php

declare(strict_types=1);

namespace JonasWindmann\EzScript;

use Exception;
use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\lexer\Lexer;
use JonasWindmann\EzScript\parser\Parser;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase{

    private array $interpreters = [];

    protected function onEnable(): void
    {
        $this->loadScripts();
    }

    protected function onDisable(): void
    {
        foreach($this->interpreters as $interpreter){
            $interpreter->stop();
        }
    }

    private function loadScripts()
    {
        $dataFolder = Server::getInstance()->getDataPath() . "/scripts/";

        if(!is_dir($dataFolder)){
            mkdir($dataFolder);
        }

        $scripts = [];
        foreach(glob($dataFolder . "*.ez") as $file){
            $scripts[] = $this->loadScript($file);
        }

        $this->getLogger()->info("Loaded " . count($scripts) . " scripts");
        $this->getLogger()->info("Starting scripts...");
        foreach($scripts as $script){
            if($script === null){
                $this->getLogger()->error("Faulty script was not started.");
                continue;
            }
            $interpreter = new Interpreter();
            $this->interpreters[] = $interpreter;
            try {
                $interpreter->run($script);
            } catch (Exception $e) {
                $this->getLogger()->error($e->getMessage());
            }
        }
    }

    private function loadScript(string $file)
    {
        $content = file_get_contents($file);

        $lexer = new Lexer($content);
        $tokens = $lexer->getTokens();

        if(!$tokens){
            $this->getLogger()->error($lexer->getError());
            return null;
        }

        $parser = new Parser($tokens);
        try {
            return $parser->parse();
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            return null;
        }
    }
}
