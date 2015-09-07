<?php

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
 * ProfileController implements the CRUD actions for UserProfile model.
 */
class ProfileController extends BaseController
{
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
                        'actions' => ['manage'],
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
     * @param string $user
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('view', $this->findModel());
    }

    /**
     * Displays a single User and UserProfile model for management
     * by administrators.
     * @param string $id
     * @return mixed
     */
    public function actionManage($id)
    {
        return $this->render('view', $this->findModel($id));
    }

    /**
     * Updates currently logged in user's profile. If update is successful, 
     * the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionUpdate()
    {
        $data = $this->findModel();
        extract($data);
        $model->scenario = Module::UI_PROFILE;
        $post = Yii::$app->request->post();
        $hasProfile = $this->getConfig('profileSettings' , 'enabled');
        if ($hasProfile) {
            $validate = $model->load($post) && $profile->load($post) && Model::validateMultiple ([$model, $profile]);
        } else {
            $validate = $model->load($post) && $model->validate();
        }
        if ($validate) {
            $model->save();
            if ($hasProfile) {
                $profile->uploadAvatar();
                $profile->save();
            }
            $action = $this->getConfig('actionSettings', Module::ACTION_PROFILE_INDEX);
            Yii::$app->session->setFlash('success', Yii::t('user', 'The user profile was updated successfully.'));
            return $this->redirect([$action]);
        } 
        return $this->render('update', [
            'model' => $model,
            'profile' => $profile
        ]);
    }

    /**
     * Deletes a profile avatar
     * @param string $user the username
     * @return mixed
     */
    public function actionAvatarDelete($user)
    {
        $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
        $model = $userClass::findByUsername($user);
        $id = $model->id;
        if ($id != Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('user', 'This operation is not allowed'));
        }
        $class = $this->getConfig('modelSettings', Module::MODEL_PROFILE);
        $profile = $class::findOne($id);
        if ($profile !== null && $profile->deleteAvatar()) {
            if ($profile->save()) {
                Yii::$app->session->setFlash('info', Yii::t('user', 'The profile avatar was deleted successfully'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('user', 'Error deleting the profile avatar'));
            }
        }
        $action = $this->getConfig('actionSettings', Module::ACTION_PROFILE_EDIT);
        return $this->redirect([$action]);
    }

    /**
     * Finds the User and UserProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id the user id (if set to null will pick currently logged in user)
     * @return array of User, UserProfile loaded models along with an 
     * array of SocialProfile models if available 
     * @throws NotFoundHttpException if the base user model cannot be found
     */
    protected function findModel($id = null)
    {
        $profile = $social = null;
        if ($id === null) {
            $model = Yii::$app->user->identity;
            $id = $model->id;
        } else {
            $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
            $model = $userClass::findOne($id);
        }
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'The requested page does not exist.'));
        }
        $profileClass = $this->getConfig('modelSettings', Module::MODEL_PROFILE);
        $socialClass = $this->getConfig('modelSettings', Module::MODEL_SOCIAL_PROFILE);
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
