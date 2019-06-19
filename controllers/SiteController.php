<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Akun;
use app\models\Nasabah;
use app\models\Pengguna;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error','confirm','reset-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
        date_default_timezone_set("Asia/Jakarta");

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        // $start = strtotime('09:00');
        // $end = strtotime('18:00');

        // if(time() >= $start && time() <= $end) {

        // } else {
        //     Yii::$app->session->setFlash('danger', "Jam Kerja Sudah Berakhir, Tidak Dapat Mengakses");
        //     return $this->redirect('index');
        // }
        
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

    public function actionConfirm($access_token)
    {
        $account = Akun::find()->where(['access_token'=>$access_token])->one();
        $nasabah = Nasabah::find()->where(['id_akun'=>$account->id])->one();
        if ($account) {
            $account->id_status_akun = 2;
            $account->access_token = Yii::$app->security->generateRandomString();
            $account->save(false);

            $this->layout = 'main-login.php';
            return $this->render('success', [
                'nasabah' => $nasabah,
            ]);
        }else{
            $this->layout = 'main-login.php';
            return $this->render('failed', [
                'nasabah' => $nasabah,
            ]);
        }

    }

    public function actionResetPassword($id)
    {
        $pengguna = Pengguna::find()->where(['id'=>$id])->one();
        $model = Akun::find()->where(['id'=>$pengguna->id_akun])->one();

        if ($model->load(Yii::$app->request->post())){
            if($model['currentPassword'] == NULL || $model['currentPassword'] == ""){
                \Yii::$app->getSession()->setFlash('danger', 'Kolom password kosong !');
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                if (Yii::$app->getSecurity()->validatePassword($model['currentPassword'], $model['password_hash'])) {
                    // jika password sama
                    $model['password_hash'] = Yii::$app->getSecurity()->generatePasswordHash($model['newPassword']);
                    $model->save(false);
                    \Yii::$app->getSession()->setFlash('success', 'Password Telah Diganti');
                    return $this->redirect(Yii::$app->request->referrer);
                }else{
                    // Jika berbeda
                    \Yii::$app->getSession()->setFlash('error', 'Maaf Password yang anda masukan tidak cocok');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }else{
            return $this->renderAjax('reset-password', [
                'model' => $model,
            ]);
        }
    }
}
