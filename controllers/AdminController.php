<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
namespace comyii\user\controllers;

use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\UserSearch;
use comyii\user\events\admin\IndexEvent;
use comyii\user\events\admin\ViewEvent;
use comyii\user\events\admin\CreateEvent;
use comyii\user\events\admin\UpdateEvent;
use comyii\user\events\admin\BatchUpdateEvent;

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
        return $this->mergeBehaviors([
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
        ]);
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $event = new IndexEvent;
        $event->searchModel = new UserSearch;
        $event->dataProvider = $event->searchModel->search(Yii::$app->request->getQueryParams());
        $this->_module->trigger(Module::EVENT_ADMIN_INDEX, $event);
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_ADMIN_INDEX, [
            'dataProvider' => $event->dataProvider,
            'searchModel' => $event->searchModel,
        ]);
    }

    /**
     * Batch update user statuses
     *
     * @return array the ajax response data that will be sent as a json format
     * @throws BadRequestHttpException
     * @throws Exception
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
        $event = new BatchUpdateEvent;
        $event->keys = $post['keys'];
        $event->status = $post['status'];
        $class = $this->fetchModel(Module::MODEL_USER);
        $event->command = $app->db->createCommand()->update(
            $class::tableName(),
            ['status' => $event->status],
            ['and', ['id' => $event->keys], 'status <> ' . Module::STATUS_SUPERUSER]
        );
        $this->_module->trigger(Module::EVENT_ADMIN_BATCH_UPDATE_BEGIN, $event);
        try {
            $event->batch();
        } catch (Exception $e) {
            $this->raise($e, $event);
        }
        $this->_module->trigger(Module::EVENT_ADMIN_BATCH_UPDATE_COMPLETE, $event);
        return [
            'status' => 'success',
            'keys' => $event->keys,
            'message' => Yii::t(
                'user',
                'The status was updated successfully for {n, plural, one{one user} other{# users}}.',
                ['n' => count($event->keys)]
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
        $event = new ViewEvent;
        $event->model = $this->findModel($id);
        $event->model->setScenario(Module::SCN_ADMIN);
        $this->_module->trigger(Module::EVENT_ADMIN_VIEW, $event);
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $this->_module->trigger(Module::EVENT_ADMIN_UPDATE_BEGIN, $event);
        }
        if ($event->model->load($post)) {
            if ($event->model->save()) {
                $event->flashType = 'success';
                $event->message = Yii::t('user', 'The user details were saved successfully', [
                    'id' => $event->model->id,
                    'user' => $event->model->username,
                ]);
                $this->_module->trigger(Module::EVENT_ADMIN_UPDATE_COMPLETE, $event);
                static::setFlash($event);
            }
        }
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_ADMIN_VIEW, [
            'model' => $event->model,
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
        $m = $this->_module;
        $event = new UpdateEvent;
        $event->model = $this->findModel($id);
        $event->model->setScenario(Module::SCN_ADMIN);
        $settings = $m->getEditSettingsAdmin($event->model);
        $this->_module->trigger(Module::EVENT_ADMIN_UPDATE_BEGIN, $event);
        if ($event->model->load(Yii::$app->request->post()) && $event->model->save()) {
            $this->_module->trigger(Module::EVENT_ADMIN_UPDATE_COMPLETE, $event);
            return $this->eventRedirect($event, ['view', 'id' => $event->model->id], false);
        } else {
            return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_ADMIN_UPDATE, [
                'model' => $event->model,
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
         * @var User   $class
         * @var User   $model
         */
        $m = $this->_module;
        $user = Yii::$app->user;
        $settings = $user->isSuperuser ? $m->superuserEditSettings : $m->adminEditSettings;
        if (!$m->checkSettings($settings, 'createUser')) {
            throw new ForbiddenHttpException(Yii::t('user', 'This operation is not allowed'));
        }
        $event = new CreateEvent;
        $this->_module->trigger(Module::EVENT_CREATE_USER_BEGIN, $event);
        $class = $this->fetchModel(Module::MODEL_USER);
        $event->model = $model = new $class(['scenario' => Module::SCN_ADMIN_CREATE]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if ($model->save()) {
                $event->flashType = 'success';
                $event->message = Yii::t('user', 'The user was created successfully.');
                $this->_module->trigger(Module::EVENT_CREATE_USER_COMPLETE, $event);
                return $this->eventRedirect($event, ['view', 'id' => $event->model->id], false);
            }
        }
        return $this->display($event->viewFile ? $event->viewFile : Module::VIEW_ADMIN_CREATE, [
            'model' => $event->model
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
