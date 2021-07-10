<?php

declare(strict_types = 1);

namespace EssentialsPE\Tasks;

use EssentialsPE\Loader;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class GeoLocation extends AsyncTask{
    /** @var Player[]|null */
    private ?array $player = null;
    /** @var array */
    private array $ip = [];

    /**
     * @param Player|Player[]|null $player
     */
    public function __construct($player){
        if($player !== null){
        	if(!is_array($player)) {
        		$player = [$player];
	        }
            foreach($player as $p){
                $spl = spl_object_hash($p);
                $this->player[$spl] = $p;
                $this->ip[$spl] = $p->getAddress();
            }
        }
    }

    public function onRun(): void{
        if($this->player === null){
            $data = Internet::getURL("https://ip-api.com/json/");
            $this->setResult(json_decode($data, true)["country"] ?? "Unknown");
        }else{
            $list = [];
            foreach($this->ip as $spl => $ip){
                $data = Internet::getURL("https://ip-api.com/json/" . $ip);
                $data = json_decode($data, true);
                if(isset($data["message"]) && $data["message"] === "private range"){
                    $data["country"] = "server";
                }
                if(isset($data["country"])){
                    $list[$spl] = $data["country"] ?? "Unknown";
                }
            }
            $this->setResult($list);
        }
    }

    /**
     * @param Server $server
     */
    public function onCompletion(Server $server): void{
        /** @var Loader $plugin */
        $plugin = $server->getPluginManager()->getPlugin("EssentialsPE");
        if(!is_array($this->getResult())){
            $plugin->getAPI()->setServerGeoLocation($this->getResult());
        }else{
            foreach($this->getResult() as $spl => $loc){
                $plugin->getAPI()->updateGeoLocation($this->player[$spl], ($loc !== "server" ?? $plugin->getAPI()->getServerGeoLocation()));
            }
        }
    }
}