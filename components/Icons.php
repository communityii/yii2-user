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
 * Class Icons the icon settings for the module.
 * 
 * @package comyii\user\components
 */
class Icons extends Component
{
    /**
     * @var string the icon CSS class prefix to use
     */
    public $prefix = 'glyphicon glyphicon-';
    
    
    /**
     * Fetch the icon for a icon identifier
     *
     * @param string $id suffix the icon suffix name
     * @param array  $options the icon HTML attributes
     * @param string $prefix the icon css prefix name
     *
     * @return string the parsed icon
     */
    public function icon($id, $options = ['style' => 'margin-right:5px'], $prefix = null)
    {
        if ($prefix === null) {
            $prefix = $this->prefix;
        }
        Html::addCssClass($options, explode(' ', $prefix . $id));
        return Html::tag('i', '', $options);
    }
}