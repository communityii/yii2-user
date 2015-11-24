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
use yii\codeception\TestCase;

//use comyii\user\Module;

class ModuleTypesTest extends TestCase
{

    use Specify;

    protected function setUp()
    {
        
    }

    protected function tearDown()
    {
        
    }

    public function testModuleInstanceOf()
    {
        $m = Yii::$app->getModule('user');

        $this->specify('Module should be instance \comyii\user\Module', function () use ($m) {
            verify($m instanceof Module)->true();
        });
    }

    public function testModuleLayouts()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module layouts settings should be different based on user type', function () use ($m) {
            $a = $m[TestUser::TYPE_BUYER]->layouts;
            $b = $m[TestUser::TYPE_SELLER]->layouts;
            verify($a instanceof Layouts)->true();
            verify($b instanceof Layouts)->true();
            // check custom settings don't match
            verify($a[Views::VIEW_LOGIN] !== $a[Views::VIEW_LOGIN])->true();
            // check defaults
            verify($a[Views::VIEW_RESET] === $a[Views::VIEW_RESET])->true();
        });
    }

    public function testModuleActions()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module layouts settings should be different based on user type', function () use ($m) {
            $a = $m[TestUser::TYPE_BUYER]->layouts;
            $b = $m[TestUser::TYPE_SELLER]->layouts;
            verify($a instanceof Layouts)->true();
            verify($b instanceof Layouts)->true();
            // check custom settings don't match
            verify($a[Views::VIEW_LOGIN] !== $a[Views::VIEW_LOGIN])->true();
            // check defaults
            verify($a[Views::VIEW_RESET] === $a[Views::VIEW_RESET])->true();
        });
    }

    public function testModuleButtons1()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module layouts settings should be different based on user type', function () use ($m) {
            $a = $m[TestUser::TYPE_BUYER]->layouts;
            $b = $m[TestUser::TYPE_SELLER]->layouts;
            verify($a instanceof Layouts)->true();
            verify($b instanceof Layouts)->true();
            // check custom settings don't match
            verify($a[Views::VIEW_LOGIN] !== $a[Views::VIEW_LOGIN])->true();
            // check defaults
            verify($a[Views::VIEW_RESET] === $a[Views::VIEW_RESET])->true();
        });
    }


    public function testModuleButtons()
    {
        $m = Yii::$app->getModule('user');
        $buttons = $m->buttons;
        $this->specify('Module buttons component should be accessible as property', function () use ($buttons) {
            verify($buttons instanceof Buttons)->true();
        });
        $this->specify('Module buttons should implement ArrayAccess', function () use ($m) {
            verify($m->get('buttons') instanceof ArrayComponent)->true();
        });
        $this->specify('Module buttons component should be instance of Buttons', function () use ($m) {
            verify($m->get('buttons') instanceof Buttons)->true();
        });
        $this->specify('Make sure all default buttons exist', function () use ($buttons) {
            verify(is_array($buttons[Buttons::BTN_HOME]))->true();
            verify(is_array($buttons[Buttons::BTN_BACK]))->true();
            verify(is_array($buttons[Buttons::BTN_RESET_FORM]))->true();
            verify(is_array($buttons[Buttons::BTN_SUBMIT_FORM]))->true();
            verify(is_array($buttons[Buttons::BTN_FORGOT_PASSWORD]))->true();
            verify(is_array($buttons[Buttons::BTN_ALREADY_REGISTERED]))->true();
            verify(is_array($buttons[Buttons::BTN_LOGIN]))->true();
            verify(is_array($buttons[Buttons::BTN_LOGOUT]))->true();
            verify(is_array($buttons[Buttons::BTN_NEW_USER]))->true();
            verify(is_array($buttons[Buttons::BTN_REGISTER]))->true();
        });
        $this->specify('Buttons::button() method should return a button', function () use ($buttons) {
            $params = [];
            $config = [];
            verify(is_string($buttons->button(Buttons::BTN_HOME, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_BACK, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_RESET_FORM, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_SUBMIT_FORM, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_FORGOT_PASSWORD, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_ALREADY_REGISTERED, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_LOGIN, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_LOGOUT, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_NEW_USER, $params, $config)))->true();
            verify(is_string($buttons->button(Buttons::BTN_REGISTER, $params, $config)))->true();
        });
//        // login view should be set to default layout
//        $this->assertTrue($layouts[Views::VIEW_LOGIN] === 'default');
//        // layout should now be set to the current (last selected layout)
//        $this->assertTrue($layouts->layout === 'default');
    }

    public function testModuleViews()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module views should be accessible as property', function () use ($m) {
            $views = $m[TestUser::TYPE_BUYER]->views;
            verify($views instanceof Views)->true();
        });
        $this->specify('Module views component should implement ArrayAccess', function () use ($m) {
            verify($m[TestUser::TYPE_BUYER]->get('views') instanceof ArrayComponent)->true();
        });
        $this->specify('Module views component should be instance of Views', function () use ($m) {
            verify($m[TestUser::TYPE_BUYER]->get('views') instanceof Views)->true();
        });
    }

    public function testModuleStatuses()
    {
        $m = Yii::$app->getModule('user');
        $this->specify('Module statuses should be accessible as property', function () use ($m) {
            $statuses = $m[TestUser::TYPE_BUYER]->statuses;
            verify($statuses instanceof Statuses)->true();
        });
        $this->specify('Module statuses component should implement ArrayAccess', function () use ($m) {
            verify($m[TestUser::TYPE_BUYER]->get('statuses') instanceof ArrayComponent)->true();
        });
        $this->specify('Module statuses component should be instance of Statuses', function () use ($m) {
            verify($m[TestUser::TYPE_BUYER]->get('statuses') instanceof Statuses)->true();
        });
    }

}
