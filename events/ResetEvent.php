<?php

namespace comyii\user\events;

class ResetEvent extends Event
{   
    /**
     * @var boolean the result of the reset attempt
     */
    public $result;
}
