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

use Yii;
use yii\base\Component;
use comyii\user\Module;

/**
 * Class Registration the registration settings for the module.
 * 
 * @package comyii\user\components
 */
class Registration extends Component
{
    /**
     * @var bool whether the registration is enabled for the module. Defaults to `true`. If set
     * to `false`, admins will need to create users. All the other registration settings will
     * be skipped if this is set to `false`.
     */
    public $enabled = true;

    /**
     * @var array|bool the settings for the captcha action, validator, and widget . If set to `false`,
     * no captcha will be displayed. The following settings can be set:
     * - `action`: array, the captcha action settings.
     * - `validator`: array, the captcha validator settings.
     * - `widget`: array, the captcha widget settings.
     * Defaults to array using `yii\captcha\CaptchaAction` with default catpcha widget
     */
    private $captcha;

    /**
     * @var bool whether account is automatically activated after registration. If set to
     * `false`, the user will need to complete activation before login. Defaults to `false`.
     */
    public $autoActivate = false;

    /**
     * @var array the yii\validators\StringValidator rules for the username. Defaults to
     * `['min' => 4, 'max' => 30]`.
     */
    public $usernameRules = ['min' => 4, 'max' => 30];

    /**
     * @var string $usernamePattern the regular expression to match for characters allowed in the username.
     * Defaults to `/^[A-Za-z0-9_\-]+$/u`.
     */
    public $usernamePattern = '/^[A-Za-z0-9_\-]+$/u';

    /**
     * @var string the error message to display if the username pattern validation fails.
     *   Defaults to `"{attribute} can contain only letters, numbers, hyphen, and underscore."`.
     */
    public $usernameValidMsg = '{attribute} can contain only letters, numbers, hyphen, and underscore.';

    /**
     * @var bool hide the password input in registration form and generate random password.
     * Defaults to `false`.
     */
    public $randomPasswords = false;

    /**
     * @var integer minimum length of a random generated password. Must be 8 or more.
     * Note that this value is used for social auth sign ups. Should be at least 8. Defaults to 10.
     */
    public $randomPasswordsMinLength = 10;

    /**
     * @var integer maximum length of a random generated password. Note that this
     * value is used for social auth sign ups. Should be greater than minimum password length above.
     * Defaults to 14.
     */
    public $randomPasswordMaxLength = 14;

    /**
     * @var bool hide the username input in registration form and generate random username.
     * Note that random usernames are generated for social auth sign ups when a nickname or handle is not available.
     * Defaults to `false`.
     */
    public $randomUsernames = false;

    /**
     * @var callable|array if set to a callable then the callable will be used to generate
     * the random username. If set to array, use as Haikunator config.
     */
    public $randomUsernameGenerator = [];

    function getEnabled()
    {
        return $this->enabled;
    }

    function getCatcha()
    {
        if ($this->captcha === null)
        {
            $captchaTemplate = <<< HTML
<div class="row" style="margin-bottom:-10px">
    <div class="col-sm-8">
        {input}
    </div>
    <div class="col-sm-4">
        {image}
    </div>
</div>
HTML;
            $u = Yii::$app->user;
            $this->captcha = [
                'action' => ['class' => 'yii\captcha\CaptchaAction'],
                'widget' => [
                    'captchaAction' => $u->actionSettings[Module::ACTION_CAPTCHA],
                    'template' => $captchaTemplate,
                    'imageOptions' => [
                        'title' => Yii::t('user', 'Click image to refresh and get a new code'),
                        'style' => 'height:40px'
                    ],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('user', 'Enter text as seen in image'),
                    ]
                ],
                'validator' => ['captchaAction' => 'user/' . $u->actions[Module::ACTION_CAPTCHA]],
            ];
        }
        if (!($this->captcha instanceof comyii\user\components\Captcha)) {
            $this->captcha = \Yii::createObject('comyii\user\components\Captcha', $this->captcha);
        }
        return $this->catcha;
    }

    function getAutoActivate()
    {
        return $this->autoActivate;
    }

    function getUsernameRules()
    {
        return $this->usernameRules;
    }

    function getUsernamePattern() {
        return $this->usernamePattern;
    }

    function getUsernameValidMsg()
    {
        return $this->usernameValidMsg;
    }

    function getRandomPasswords() {
        return $this->randomPasswords;
    }

    function getRandomPasswordsMinLength() {
        return $this->randomPasswordsMinLength;
    }

    function getRandomPasswordMaxLength() {
        return $this->randomPasswordMaxLength;
    }

    function getRandomUsernames() {
        return $this->randomUsernames;
    }

    function getRandomUsernameGenerator() {
        return $this->randomUsernameGenerator;
    }

}
