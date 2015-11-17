<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

use yii\base\Component;

/**
 * Class Models the status settings for the module.
 * 
 * @package comyii\user\components
 */
class Datetime extends Component
{
    /**
     * @var \Closure an anonymous function that will return current timestamp
     * for populating the timestamp fields. Defaults to 
     * `function() { return time(); }`
     */
    public $now;
    
    /**
     * @var string the datetime format in which timestamps are stored to database. Use Yii notation
     * to assign formats. Formats prepended with `php:` will use PHP DateTime format, while others
     * will be parsed as ICU notation.
     */
    public $saveFormat = 'php:U';
    
    /**
     * @var string the datetime format in which timestamps are displayed. Use Yii notation to
     * assign formats. Formats prepended with `php:` will use PHP DateTime format, while others
     * will be parsed as ICU notation.
     */
    public $displayFormat = 'php:M d, Y H:i';
    
    public function __construct($config = array()) {
        if (!isset($config['now'])) {
            $this->now = function() { return time(); };
        }
        parent::__construct($config);
    }
}

