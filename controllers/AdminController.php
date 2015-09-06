<?php

namespace comyii\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use comyii\user\Module;
use comyii\user\models\User;
use comyii\user\models\UserSearch;
use comyii\user\controllers\BaseController;

/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends BaseController
{
    /**
     * Admin controller behaviors
     */
    public function behaviors()
    {
        $user = Yii::$app->user;
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => $user->isAdmin || $user->isSuperuser 
                    ],
                ],
            ]
        ];
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Manages a single User model (view and edit).
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Module::UI_ADMIN);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->save()) {
                $this->module->setFlash('success', 'user-details-saved', [
                    'id' => $model->id,
                    'user' => $model->username,
                ]);
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Batch update statuses for User.
     * @return mixed
     */
    public function actionBatchUpdate()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new BadRequestHttpException('This operation is not allowed');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if (empty($post) || empty($post['keys'])) {
            return null;
        }
        $keys = $post['keys'];
        $status = $post['status'];
        $user = User::tableName();
        Yii::$app->db->createCommand()->update($user, ['status' => $status], ['and', ['id' => $keys], 'status <> ' . User::STATUS_SUPERUSER])->execute();
        return $keys;
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
        $model = new $userClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $userClass = $this->getConfig('modelSettings', Module::MODEL_USER);
        if (($model = $userClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
