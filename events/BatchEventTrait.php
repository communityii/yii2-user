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

trait BatchEventTrait
{
    /**
     * @var array the array of callbacks to apply to each record.
     */
    protected $_callbacks;
    /**
     * @var array the record IDs 
     */
    public $keys;
    /**
     * @var string the model class 
     */
    public $class;
    /**
     *
     * @var integer how many records to update for each callback loop 
     */
    public $batchSize = 25;
    
    /**
     * 
     * @return type
     */
    public function batch()
    {
        if (empty($this->_callbacks)) {
            return;
        }
        $class = $this->class;
        foreach ($class::find($this->keys)->each($this->batchSize) as $model) {
            $this->updateModel($model);
        }
    }
    
    /**
     * Apply callback functions to model
     * @param \yii\db\Model $model
     */
    public function updateModel($model)
    {
        foreach ($this->_callbacks as $func)
        {
            call_user_func($func, $model);
        }
    }
    
    /**
     * Add callback
     * @param callable $callback
     */
    public function attachCallback($callback)
    {
        $this->_callbacks[] = $callback;
    }
}
