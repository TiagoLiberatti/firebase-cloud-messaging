<?php

namespace app\controllers;

use app\models\TokenNotificacao;
use http\Url;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSaveToken(){
        $token = Yii::$app->request->post('token');
        $model = new TokenNotificacao();
        $model->token = $token;
        if ($model->save()){
            $this->sendRequestTopic($token);
            return true;
        }else{
            var_dump($model->getErrors());
            return false;
        }
    }

    private function sendRequestTopic($token){
        $url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/all3';
        $server_key = 'AAAAJ0-rZTQ:APA91bGLtTrWBJKqDCO_McAom6ItR8qgnZMq0r-OT5yxKkubZMu1kWmWbwClOQ_2L-jpM--T4ovSS25Kc_YN69o0zQ6oS8EIVfwPu12Gqb-tRCQxnw5uG8SGOdOuZbMlD4WL1l9PBGMF';
        $headers = [
            'Authorization:key='.$server_key
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Erro: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function actionSendNotification(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = TokenNotificacao::find()->select(['token'])->asArray()->all();
        $result = [];
        $img = \yii\helpers\Url::to('@web/img/1517970.png');
        foreach ($data as $d){
            $data = [
                    "to" => $d['token'],
                    //"to" => "/topics/all3",
                    "notification" => [
                        "body" => "Teste",
                        "title" => "Teste 2",
                        //"click_action" => "http://www.uol.com.br",
                        "icon" => $img
                    ],
            ];
            $result[] = $this->sendRequest($data);
        }
        return $result;
    }

    private function sendRequest($data){
        $data = json_encode($data);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAJ0-rZTQ:APA91bGLtTrWBJKqDCO_McAom6ItR8qgnZMq0r-OT5yxKkubZMu1kWmWbwClOQ_2L-jpM--T4ovSS25Kc_YN69o0zQ6oS8EIVfwPu12Gqb-tRCQxnw5uG8SGOdOuZbMlD4WL1l9PBGMF';
        $headers = [
            'Content-Type:application/json',
            'Authorization:key='.$server_key
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Erro: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

}