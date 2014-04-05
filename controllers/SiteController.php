<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\LoginForm;
use frontend\models\ContactForm;
use frontend\models\SignupForm;
use frontend\models\Setup;
use frontend\models\Demo;
use frontend\models\Demo1;
use frontend\models\City;
use common\models\User;
use yii\web\HttpException;
use yii\helpers\Security;
use yii\helpers\Json;
use yii\db\Query;

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'home';
        return $this->render('index');
    }

    public function actionStarRating()
    {
        return $this->render('star-rating');
    }

    public function actionStarRatingDemo()
    {
        return $this->render('star-rating', ['type' => 'demo']);
    }

    public function actionFileInput()
    {
        return $this->render('file-input');
    }

    public function actionFileInputDemo()
    {
        return $this->render('file-input', ['type' => 'demo']);
    }

    public function actionStrengthMeter()
    {
        return $this->render('strength-meter');
    }

    public function actionStrengthMeterDemo()
    {
        return $this->render('strength-meter', ['type' => 'demo']);
    }

}