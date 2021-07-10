<?php

declare(strict_types = 1);

namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\scheduler\Task;

abstract class BaseTask extends Task{
    /** @var BaseAPI */
    private BaseAPI $api;

    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        $this->api = $api;
    }

    /**
     * @return Loader
     */
    public final function getPlugin(): Loader{
        return $this->getAPI()->getEssentialsPEPlugin();
    }

    /**
     * @return BaseAPI
     */
    public final function getAPI(): BaseAPI{
        return $this->api;
    }
}