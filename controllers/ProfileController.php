<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
namespace comyii\user\controllers;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use comyii\user\Module;
use comyii\user\components\EmailException;
use comyii\user\models\User;
use comyii\user\models\UserProfile;
use comyii\user\models\SocialProfile;
use comyii\user\events\profile\IndexEvent;
use comyii\user\events\profile\ViewEvent;
use comyii\user\events\profile\UpdateEvent;
use comyii\user\events\profile\AvatarDeleteEvent;

/**
 * ProfileController implements the view, update, and avatar management actions for a UserProfile model.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ProfileController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $user = Yii::$app->user;
        return $this->mergeBehaviors([
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'avatar-delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => $user->isSuperuser || $user->isAdmin,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'avatar-delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Displays current user profile
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $event = new IndexEvent;
        $event->extract($this->findModel());
        $this->_module->trigger(Module::EVENT_PROFILE_INDEX, $event);
        if (Yii::$app->request->post()) {
            $out = $this->update($event->getModels());
            if ($out !== null) {
                return $out;
            }
        }
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_PROFILE_INDEX, [
            'model' => $event->model,
            'profile' => $event->profile,
            'social' => $event->social
        ]);
    }

    /**
     * Displays a single User and UserProfile model for management by administrators.
     *
     * @param string $id the user id - this is considered only when current logged in user is a superuser or admin.
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $event = new ViewEvent;
        $event->extract($this->findModel($id));
        $this->_module->trigger(Module::EVENT_PROFILE_INDEX, $event);
        if (Yii::$app->request->post()) {
            $out = $this->update($event->getModels());
            if ($out !== null) {
                return $out;
            }
        }
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_PROFILE_VIEW, [
            'model' => $event->model,
            'profile' => $event->profile,
            'social' => $event->social
        ]);
    }

    /**
     * Updates currently logged in user's profile. If update is successful, the browser will be redirected to the
     * 'index' page.
     *
     * @return mixed
     */
    public function actionUpdate()
    {
        $event = new UpdateEvent;
        $event->extract($this->findModel());
        $this->_module->trigger(Module::EVENT_PROFILE_UPDATE, $event);
        if (Yii::$app->request->post()) {
            $out = $this->update($event->getModels());
            if ($out !== null) {
                return $out;
            }
        }
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_PROFILE_UPDATE, [
            'model' => $event->model,
            'profile' => $event->profile,
            'social' => $event->social
        ]);
    }

    /**
     * Deletes a profile avatar
     *
     * @param string $user the username
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAvatarDelete($user)
    {
        /**
         * @var User        $userClass
         * @var User        $model
         * @var UserProfile $class
         * @var UserProfile $profile
         */
        $event = new AvatarDeleteEvent;
        $userClass = $this->fetchModel(Module::MODEL_USER);
        $event->model = $userClass::findByUsername($user);
        $id = null;
        if ($event->model !== null) {
            $id = $event->model->id;
        }
        if ($id === null || $id != Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('user', 'This operation is not allowed'));
        }
        $class = $this->fetchModel(Module::MODEL_PROFILE);
        $profile = $class::findOne($id);
        $event->profile = $profile;
        $this->_module->trigger(Module::EVENT_PROFILE_DELETE_AVATAR_BEGIN, $event);
        if ($event->profile !== null && $event->profile->deleteAvatar()) {
            if ($event->profile->save()) {
                $event->flashType = 'info';
                $event->message = Yii::t('user', 'The profile avatar was deleted successfully');
            } else {
                $event->flashType = 'error';
                $event->message = Yii::t('user', 'Error deleting the profile avatar');
            }
        }

        $this->_module->trigger(Module::EVENT_PROFILE_DELETE_AVATAR_COMPLETE, $event);
        static::setFlash($event);
        $action = $this->fetchAction(Module::ACTION_PROFILE_UPDATE);
        return $this->eventRedirect($event, [$action]);
    }

    /**
     * Finds the User and UserProfile model based on its primary key value. If the model is not found, a 404 HTTP
     * exception will be thrown.
     *
     * @param integer $id the user id (if set to null will pick the currently logged in user)
     *
     * @return array of the User, related UserProfile model and an array of related SocialProfile models for the user
     * @throws NotFoundHttpException if the base user model cannot be found
     */
    protected function findModel($id = null)
    {
        /**
         * @var User          $userClass
         * @var UserProfile   $profileClass
         * @var SocialProfile $socialClass
         */
        if ($id === null) {
            $user = Yii::$app->user;
            $model = $user->identity;
            $id = $user->id;
        } else {
            $userClass = $this->fetchModel(Module::MODEL_USER);
            $model = $userClass::findIdentity($id);
        }
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'The requested page does not exist.'));
        }
        $profileClass = $this->fetchModel(Module::MODEL_PROFILE);
        $socialClass = $this->fetchModel(Module::MODEL_SOCIAL_PROFILE);
        $social = null;
        $profile = $profileClass::findOne($id);
        if ($profile === null) {
            $profile = new $profileClass();
            $profile->id = $id;
        }
        if ($this->getConfig('socialSettings', 'enabled', false)) {
            $social = $socialClass::find()->where(['user_id' => $id])->all();
            if (!$social) {
                $social = [new $socialClass(['user_id' => $id])];
            }
        }
        return ['model' => $model, 'profile' => $profile, 'social' => $social];
    }

    /**
     * Updates an user profile
     *
     * @param array $data the profile data attributes
     *
     * @return \yii\web\Response|null
     * @throws EmailException
     */
    protected function update($data)
    {
        $event = new UpdateEvent;
        $event->extract($data);
        $event->model->scenario = Module::SCN_PROFILE;
        $post = Yii::$app->request->post();
        $hasProfile = $this->_module->getProfileSetting('enabled');
        $emailOld = $event->model->email;
        $this->_module->trigger(Module::EVENT_PROFILE_UPDATE_BEGIN, $event);
        $transaction = static::tranInit($event);
        try {
            if ($hasProfile || isset($post['UserProfile'])) {
                $validate = $event->model->load($post) && $event->profile->load($post) && Model::validateMultiple([
                        $event->model,
                        $event->profile
                    ]);
            } else {
                $validate = $event->model->load($post) && $event->model->validate();
            }
            if ($validate) {
                $timeLeft = Module::timeLeft('email change confirmation', $event->model->getEmailChangeKeyExpiry());
                $emailNew = null;
                if ($event->model->validateEmailChange($emailOld)) {
                    $emailNew = $event->model->email_new;
                }
                $event->model->save();
                if ($hasProfile || isset($post['UserProfile'])) {
                    $event->profile->uploadAvatar();
                    $event->profile->save();
                }
                $event->flashType = 'success';
                $event->message = Yii::t('user', 'The user profile was updated successfully.');
                $this->_module->trigger(Module::EVENT_PROFILE_UPDATE_COMPLETE, $event);
                $action = $this->fetchAction(Module::ACTION_PROFILE_INDEX);
                self::setFlash($event);
                if (!$event->model->sendEmail('newemail', $timeLeft)) {
                    throw new EmailException(Yii::t(
                        'user',
                        'Your email change to <b>{email}</b> could not be processed. Please contact the system administrator or try again later.',
                        ['email' => $emailNew]
                    ));
                }
                static::tranCommit($transaction);
                Yii::$app->session->setFlash('info', Yii::t(
                    'user',
                    'Instructions to confirm the new email has been sent to your new email address <b>{email}</b>. {timeLeft}',
                    ['email' => $emailNew, 'timeLeft' => $timeLeft]
                ));
                return $this->eventRedirect($event, [$action], false);
            }
        } catch (Exception $e) {
            $this->handleException($e);
            static::tranRollback($transaction);
        }
        return null;
    }
}
