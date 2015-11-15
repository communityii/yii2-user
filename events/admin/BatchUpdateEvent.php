<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\events\admin;

use Closure;
use yii\db\ActiveRecord;
use yii\db\Command;
use comyii\user\events\RecordEvent;
use comyii\user\events\BatchEventInterface;

/**
 * Class BatchUpdateEvent is used for triggering events when batch updating multiple user model records
 *
 * @package comyii\user\events\admin
 */
class BatchUpdateEvent extends RecordEvent implements BatchEventInterface
{
    /**
     * @var array the array of callbacks to apply to each record.
     */
    protected $_callbacks;

    /**
     * @var array the record identifiers (set as primary key attribute => list of values)
     */
    public $keys;

    /**
     * @var string the model class
     */
    public $modelClass;

    /**
     * @var int the user status
     */
    public $status;
    
    /**
     * @var Command the database command
     */
    public $command;

    /**
     *
     * @var int how many records to update for each callback loop
     */
    public $batchSize = 25;

    /**
     * Batch process the models
     */
    public function batch()
    {
        if (empty($this->_callbacks)) {
            return;
        }
        /**
         * @var ActiveRecord $class
         */
        $class = $this->modelClass;
        $models = $class::find()->where($this->keys);
        foreach ($models->each($this->batchSize) as $model) {
            $this->updateModel($model);
        }
    }

    /**
     * Apply callback functions to model
     *
     * @param ActiveRecord $model
     */
    public function updateModel($model)
    {
        foreach ($this->_callbacks as $func) {
            call_user_func($func, $model);
        }
    }

    /**
     * Add a callback to the callbacks stack
     *
     * @param Closure $callback
     */
    public function attachCallback($callback)
    {
        $this->_callbacks[] = $callback;
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (isset($this->command)) {
            if (!$this->command instanceof Command) {
                throw new \yii\base\InvalidConfigException('Command must be of type `yii\db\Command`');
            }
            $this->command->execute();
        }
    }
}
