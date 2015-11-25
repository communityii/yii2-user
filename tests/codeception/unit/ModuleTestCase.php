<?php
namespace comyii\user\tests;

use Codeception\Specify;
use Codeception\Util\Debug;
use Codeception\Util\Logger;
use comyii\common\components\ArrayComponent;
use comyii\user\components\Actions;
use comyii\user\components\Buttons;
use comyii\user\components\Layouts;
use comyii\user\components\Statuses;
use comyii\user\components\Views;
use comyii\user\Module;
use Yii;
use yii\base\Component;
use yii\codeception\TestCase;

//use comyii\user\Module;

class ModuleTestCase extends TestCase
{
    use Specify;

    protected function setUp()
    {
        
    }

    protected function tearDown()
    {
    }
    
    public function checkComponent($m, $name, $class, $array = false)
    {
        $this->specify('Module '.$name.' should be accessible as property', function () use ($m, $name) {
            $component = $m->{$name};
            verify($component instanceof Component)->true();
        });
        $this->specify('Module '.$name.' component should be instance of '.$class, function () use ($m, $name, $class) {
            verify($m->get($name) instanceof $class)->true();
        });
        if ($array) {
            $this->specify('Module statuses component should implement ArrayAccess', function () use ($m, $name) {
                verify($m->get($name) instanceof ArrayComponent)->true();
            });
        }
    }
}