<?php

declare(strict_types = 1);

namespace EssentialsPE\Events;


use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCustomEvent;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerNickChangeEvent extends BaseCustomEvent implements Cancellable{
    public static $handlerList = null;

    /** @var Player  */
    protected Player $player;
    /** @var  null|string */
    protected ?string $new_nick;
    /** @var  string */
    protected string $old_nick;
    /** @var bool|mixed  */
    protected $nametag;

    /**
     * @param BaseAPI $api
     * @param Player $player
     * @param string $new_nick
     * @param mixed $nameTag
     */
    public function __construct(BaseAPI $api, Player $player, string $new_nick, $nameTag = false){
        parent::__construct($api);
        $this->player = $player;
        $this->new_nick = $new_nick;
        $this->old_nick = $player->getDisplayName();
        $this->nametag = ($nameTag ?? $new_nick);
    }

    /**
     * Return the player to be used
     *
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * Return the new nick to be set
     *
     * @return string
     */
    public function getNewNick(): string{
        return $this->new_nick . "§r";
    }

    /**
     * Tell the actual nick of the player
     *
     * @return string
     */
    public function getOldNick(): string{
        return $this->old_nick;
    }

    /**
     * Change the nick to be set
     *
     * @param string $nick
     */
    public function setNick(string $nick): void{
        $this->new_nick = $nick . "§r";
    }

    /**
     * Return the NameTag to be set
     * Usually it's the same has the new nick, but plugins can use it to modify the NameTag too
     *
     * @return bool|string
     */
    public function getNameTag(){
        return $this->nametag;
    }

    /**
     * Change the NameTag to be set
     *
     * @param string|null $nametag
     */
    public function setNameTag(?string $nametag): void{
        $this->nametag = $nametag;
    }
}
