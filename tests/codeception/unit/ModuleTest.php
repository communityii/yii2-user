<?php
namespace comyii\user\tests;

use Codeception\Specify;
use yii\codeception\TestCase;
use Codeception\Util\Debug;
use Codeception\Util\Logger;
use Yii;
use comyii\user\components\Views;

//use comyii\user\Module;

class ModuleTest extends TestCase
{
    use Specify;
    protected function setUp()
    {
        Yii::$app->getModule('user');
    }

    protected function tearDown()
    {
    }

    public function testModuleInstanceOf()
    {
        $m = Yii::$app->getModule('user');
//        $m = Module::getInstance();
        
        $this->specify('Module should be instance \comyii\user\Module', function () use ($m) {
            verify($m instanceof \comyii\user\Module)->true();
        });
//        $this->specify('Module has components property and definition array', function () use ($m) {
//             verify($m->getComponents() === func() && is_array($m->$m->getComponents(true)))->true();
//             
//        });
    }
    
    public function testModuleLayouts()
    {
        $m = Yii::$app->getModule('user');
        $layouts = $m->layouts;
        $default = $layouts->default;
        $currentDefault = $layouts->layout;
        $loginLayout = $layouts[Views::VIEW_LOGIN];
        $current = $layouts->layout;
        $this->assertTrue($layouts instanceof \comyii\user\components\Layouts);
        $this->assertTrue($default === 'default');
        $this->assertTrue($currentDefault === null);
//        $this->assertTrue($loginLayout === 'custom');
        Debug::debug($loginLayout);
//        $this->assertTrue($current === 'custom');
        
//        $this->specify('Module layouts property should implement ArrayAcces', function () use ($m) {
//            verify($m->get('layouts') instanceof \comyii\common\components\ArrayComponent)->true();
//        });
//        $this->specify('Module layouts property should be instance of Layouts', function () use ($m) {
//            verify($m->get('layouts') instanceof \comyii\user\components\Layouts)->true();
//        });
    }
    
    public function testModuleViews()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module views should be accessible as property', function () use ($m) {
            $views = $m->views;
            verify($views instanceof \comyii\user\components\Views)->true();
        });
        $this->specify('Module views property should implement ArrayAcces', function () use ($m) {
            verify($m->get('views') instanceof \comyii\common\components\ArrayComponent)->true();
        });
        $this->specify('Module views property should be instance of Layouts', function () use ($m) {
            verify($m->get('views') instanceof \comyii\user\components\Views)->true();
        });
    }

}