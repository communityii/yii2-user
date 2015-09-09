<?php
namespace comyii\user\models;

use Yii;
use yii\base\Model;
use comyii\user\Module;
use comyii\user\models\User;

/**
 * Password reset request form
 */
class RecoveryForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $m = Yii::$app->getModule('user');
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => $m->modelSettings[Module::MODEL_USER],
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t('user', 'There is no user registered with the email!')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @param string $timeLeft the action link expiry information
     * @param User $user the user model
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($timeLeft, $user)
    {
        if ($user) {
            if ($user && !$class::isKeyValid($user->reset_key, $user->resetKeyExpiry)) {
                $user->generateResetKey();
            }
            if ($user->save()) {
                return $m->sendEmail('recovery', $user, ['timeLeft' => $timeLeft]);
            }
        }
        return false;
    }
}
