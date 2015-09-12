<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
namespace comyii\user\controllers;

use Yii;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\UserProfile;
use comyii\user\models\SocialProfile;
use comyii\user\models\UserProfileSearch;
use comyii\user\controllers\BaseController;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

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
        return [
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
        ];
    }

    /**
     * Displays current user profile
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->display(Module::VIEW_PROFILE_INDEX, $this->findModel());
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
        return $this->display(Module::VIEW_PROFILE_VIEW, $this->findModel($id));
    }

    /**
     * Updates currently logged in user's profile. If update is successful, the browser will be redirected to the
     * 'index' page.
     *
     * @return mixed
     */
    public function actionUpdate()
    {
        /**
         * @var User          $model
         * @var UserProfile   $profile
         * @var SocialProfile $social
         */
        $data = $this->findModel();
        $model = $profile = $social = null;
        extract($data);
        $model->scenario = Module::SCN_PROFILE;
        $post = Yii::$app->request->post();
        $hasProfile = $this->getConfig('profileSettings', 'enabled');
        $emailOld = $model->email;
        if ($hasProfile) {
            $validate = $model->load($post) && $profile->load($post) && Model::validateMultiple([$model, $profile]);
        } else {
            $validate = $model->load($post) && $model->validate();
        }
        if ($validate) {
            $timeLeft = Module::timeLeft('email change confirmation', $model->emailChangeKeyExpiry);
            $emailSent = $emailNew = null;
            if ($model->validateEmailChange($emailOld)) {
                $emailNew = $model->email_new;
                $emailSent = $model->sendEmail('newemail', $timeLeft);
            }
            $model->save();
            if ($hasProfile) {
                $profile->uploadAvatar();
                $profile->save();
            }
            $action = $this->fetchAction(Module::ACTION_PROFILE_INDEX);
            Yii::$app->session->setFlash('success', Yii::t('user', 'The user profile was updated successfully.'));
            if ($emailSent === true) {
                Yii::$app->session->setFlash('info', Yii::t(
                    'user',
                    'Instructions to confirm the new email has been sent to your new email address <b>{email}</b>. {timeLeft}',
                    ['email' => $emailNew, 'timeLeft' => $timeLeft]
                ));
                return $this->redirect([$action]);
            } elseif ($emailSent === false) {
                Yii::$app->session->setFlash('warning', Yii::t(
                    'user',
                    'Your email change to <b>{email}</b> could not be processed. Please contact the system administrator or try again later.',
                    ['email' => $emailNew]
                ));
            }
        }
        return $this->display(Module::VIEW_PROFILE_UPDATE, [
            'model' => $model,
            'profile' => $profile
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
        $userClass = $this->fetchModel(Module::MODEL_USER);
        $model = $userClass::findByUsername($user);
        $id = null;
        if ($model !== null) {
            $id = $model->id;
        }
        if ($id === null || $id != Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('user', 'This operation is not allowed'));
        }
        $class = $this->fetchModel(Module::MODEL_PROFILE);
        $profile = $class::findOne($id);
        $session = Yii::$app->session;
        if ($profile !== null && $profile->deleteAvatar()) {
            if ($profile->save()) {
                $session->setFlash('info', Yii::t('user', 'The profile avatar was deleted successfully'));
            } else {
                $session->setFlash('error', Yii::t('user', 'Error deleting the profile avatar'));
            }
        }
        $action = $this->fetchAction(Module::ACTION_PROFILE_UPDATE);
        return $this->redirect([$action]);
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
        $profile = $social = null;
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
        if ($this->getConfig('profileSettings', 'enabled', false)) {
            $profile = $profileClass::findOne($id);
        }
        if ($profile === null) {
            $profile = new $profileClass();
            $profile->id = $id;
        }
        if ($this->getConfig('socialSettings', 'enabled', false)) {
            $social = $socialClass::find()->where(['user_id' => $id])->all();
        }
        if (!$social) {
            $social = [new $socialClass(['user_id' => $id])];
        }
        return ['model' => $model, 'profile' => $profile, 'social' => $social];
    }
}
