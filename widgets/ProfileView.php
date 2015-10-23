<?php

namespace comyii\user\widgets;

use Yii;
use yii\bootstrap\Widget;

class ProfileView extends Widget
{
    public $model;
    public $profile;
    public $social;
    public $hasSocial;
    public $hasProfile;
    public $editSettings;
    public $profileSettings = [];
    
    public function init() {
        parent::init();
        $m = Yii::$app->getModule('user');
        $this->hasSocial = $m->socialSettings['enabled'];
        $this->editSettings = $m->getEditSettingsUser($this->model);
    }
    
    public function run() {
        $m = Yii::$app->getModule('user');
        $socialDetails = $authClients = $avatar = $profile = '';
        
        $accountDetails = AccountDetails::widget([
            'model'=>$this->model,
        ]);
        // render social details
        if ($m->socialSettings['enabled']) {
            $socialDetails = SocialDetails::widget([
                'social'=>$this->social,
                'profile'=>$this->profile,
            ]);
            if (Yii::$app->user->id == $this->model->id) {
                $authClients = SocialConnect::widget([
                    'model'=>$this->model,
                    'profile'=>$this->profile
                ]);
            }
        }
        if ($m->profileSettings['enabled']):
            $avatar = AvatarImage::widget(['profile'=>$this->profile]);
            $profile = ProfileDetails::widget(['profile' => $this->profile]);
            ?>
            <div class="row">
                <div class="col-md-2 text-center">
                    <?= $avatar ?>
                </div>
                <div class="col-md-10">
                    <div class="row">   
                        <div class="col-md-6">
                            <?= $profile ?>
                            <?= $socialDetails ?>
                        </div>
                        <?php if ($m->socialSettings['widgetEnabled']): ?>
                        <div class="col-md-6">
                            <?= $accountDetails ?>
                            <?= $authClients ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-<?= $m->socialSettings['enabled'] ? 6 : 12 ?>">
                    <?= $accountDetails ?>
                </div>
                <?php if ($m->socialSettings['enabled']): ?>
                <div class="col-md-6">
                    <?= $socialDetails ?>
                </div>
                <?php endif;?>
            </div>
        <?php endif;
    }
}

