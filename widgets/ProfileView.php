<?php

namespace comyii\user\widgets;

use Yii;
use comyii\user\models\User;
use comyii\user\models\UserProfile;
use comyii\user\models\SocialProfile;

class ProfileView extends Widget
{
    /**
     * @var User the user model
     */
    public $model;

    /**
     * @var UserProfile the userprofile model
     */
    public $profile;

    /**
     * @var SocialProfile the social profile model
     */
    public $social;

    /**
     * @var bool whether to display the social connections
     */
    public $hasSocial;

    /**
     * @var bool whether to display the profile details
     */
    public $hasProfile;

    /**
     * @var array the settings for the profile
     */
    public $profileSettings = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $m = $this->_module;
        if (!isset($this->hasSocial)) {
            $this->hasSocial = $m->socialSettings['enabled'];
        }
        if (!isset($this->hasProfile)) {
            $this->hasProfile = $m->profileSettings['enabled'];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $socialDetails = $authClients = $avatar = $profile = $details = '';
        $accountDetails = AccountDetails::widget(['model' => $this->model]);
        if ($this->hasSocial) {
            $avatar = AvatarImage::widget(['profile' => $this->profile]);
            $socialDetails = SocialDetails::widget(['social' => $this->social, 'profile' => $this->profile]);
            if (Yii::$app->user->id == $this->model->id) {
                $authClients = SocialConnect::widget([
                    'model' => $this->model,
                    'profile' => $this->profile
                ]);
            }
            if ($this->_module->socialSettings['widgetEnabled']) {
                $details = <<< HTML
                    <div class="col-md-6">
                        {$accountDetails}
                        {$authClients}
                    </div>
HTML;
            }
        }

        if ($this->hasProfile) {
            $profile = ProfileDetails::widget(['profile' => $this->profile]);
            $out = <<< HTML
                <div class="row">
                    <div class="col-md-2 text-center">
                        {$avatar}
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                               {$profile}
                               {$socialDetails}
                            </div>
                            {$details}
                        </div>
                    </div>
                </div>
HTML;
        } else {
            $col = 12;
            if ($this->hasSocial) {
                $details = '<div class="col-md-6">' . $socialDetails . '</div>';
                $col = 6;
            }
            $out = <<< HTML
                <div class="row">
                    <div class="col-md-{$col}">
                        {$accountDetails}
                    </div>
                    {$details}
                </div>
HTML;
        }
        echo $out;
    }
}
