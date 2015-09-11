<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
namespace comyii\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\UserSearch;
use comyii\user\controllers\BaseController;

/**
 * AdminController implements the CRUD actions for User model for an admin and superuser
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AdminController extends BaseController
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
                        'allow' => $user->isAdmin || $user->isSuperuser,
                        'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'batch-update' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->display(Module::VIEW_ADMIN_INDEX, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Batch update user statuses
     *
     * @return array the ajax response data that will be sent as a json format
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionBatchUpdate()
    {
        /**
         * @var User $class
         */
        $app = Yii::$app;
        $request = $app->request;
        if (!$request->isAjax) {
            throw new BadRequestHttpException('This operation is not allowed');
        }
        $app->response->format = Response::FORMAT_JSON;
        $post = $request->post();
        if (empty($post) || empty($post['keys']) || empty($post['status'])) {
            return [
                'status' => 'danger',
                'keys' => null,
                'message' => Yii::t('user', 'No valid user or status was selected for update.')
            ];
        }
        $keys = $post['keys'];
        $status = $post['status'];
        $class = $this->fetchModel(Module::MODEL_USER);
        $app->db->createCommand()->update(
            $class::tableName(),
            ['status' => $status],
            ['and', ['id' => $keys], 'status <> ' . Module::STATUS_SUPERUSER]
        )->execute();
        return [
            'status' => 'success',
            'keys' => $keys,
            'message' => Yii::t(
                'user',
                'The status was updated successfully for {n, plural, one{one user} other{# users}}.',
                ['n' => count($keys)]
            )
        ];
    }

    /**
     * Views a single User model
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Module::SCN_ADMIN);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'The user details were saved successfully', [
                    'id' => $model->id,
                    'user' => $model->username,
                ]));
            }
        }
        return $this->display(Module::VIEW_ADMIN_VIEW, [
            'model' => $model,
        ]);
    }

    /**
     * Updates an User model. If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /**
         * @var Module $m
         */
        $m = $this->module;
        $model = $this->findModel($id);
        $model->setScenario(Module::SCN_ADMIN);
        $settings = $m->getEditSettingsAdmin($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->display(Module::VIEW_ADMIN_UPDATE, [
                'model' => $model,
                'settings' => $settings
            ]);
        }
    }

    /**
     * Creates a new User model. If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        /**
         * @var Module $m
         * @var User   $model
         */
        $m = $this->module;
        $user = Yii::$app->user;
        $settings = $user->isSuperuser ? $m->superuserEditSettings : $m->adminEditSettings;
        if (!$m->checkSettings($settings, 'createUser')) {
            throw new ForbiddenHttpException(Yii::t('user', 'This operation is not allowed'));
        }
        $class = $this->fetchModel(Module::MODEL_USER);
        $model = new $class(['scenario' => Module::SCN_ADMIN_CREATE]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'The user was created successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->display(Module::VIEW_ADMIN_CREATE, [
            'model' => $model
        ]);
    }

    /**
     * Finds the User model based on its primary key value. If the model is not found, a 404 HTTP exception will be
     * thrown.
     *
     * @param string $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /**
         * @var User $class
         */
        $class = $this->fetchModel(Module::MODEL_USER);
        if (($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
