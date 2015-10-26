<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */
namespace comyii\user\events;

use \yii\db\Command;

trait CommandEventTrait
{
    /**
     * @var Command the database command
     */
    public $command;
    
    public function run()
    {
        if ($this->command) {
            if (!$this->command instanceof Command) {
                throw new \yii\base\InvalidConfigException('Command must be of type `yii\db\Command`');
            }
            $this->command->execute();
        }
    }
}
