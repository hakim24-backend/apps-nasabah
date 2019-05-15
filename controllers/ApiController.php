<?php

namespace app\controllers;

class ApiController extends \yii\rest\Controller
{
    public function behaviors(){
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'except' => ['login','registrasi']
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        $this->enableCsrfValidation = false;
        date_default_timezone_set('Asia/Jakarta');

        return parent::beforeAction($action);
    }

    public function actionRegister(){

        $param = \Yii::$app->request->post();
        $param['photo'] = isset($_FILES['photo']) ? $_FILES['photo'] : null;

        // var_dump($param);die;

        $errorMsg = array();

        if($param['password'] != "" && $param['name'] != "" && $param['sex'] != "" && $param['email'] != "" && $param['phone'] != "" && $param['department'] != "" && $param['expertise'] != "" && $param['company'] != ""){

            $user = User::find()->where(['email' => $param['email']])->one();

            if($user == null){
                $user = new User();
                $user_profile = new UserProfile();

                $name = $this->split_name($param['name']);

                $user->generateAuthKey();
                $user->setPassword($param['password']);
                $user->email = $param['email'];
                $user->created_at = Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'));
                $user->updated_at = Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'));
                $user->role = 5;
                $user->status = 0;

                if($user->save()){
                    $user_profile->gender = $param['sex'];
                    $user_profile->phone = $param['phone'];
                    $user_profile->first_name = $name[0];
                    $user_profile->last_name = $name[1];
                    $user_profile->id_departement = $param['department'];
                    $user_profile->id_jobtitle = $param['expertise'];
                    $user_profile->id_user = $user->id;
                    $user_profile->id_perusahaan = $param['company'];

                    if(isset($param['photo'])){

                        $extension = $this->getFileExtension($param['photo']['name']);
                        $name = "photo-".$user->id."-".time(). '.' . $extension;
                        $filedest = Yii::getAlias('@frontend/web/user/') . $name;
                        move_uploaded_file($param['photo']['tmp_name'], $filedest);
                        $user_profile->photo = $name;
                    }

                    if($user_profile->save(false)){
                        $email = \Yii::$app->mailer->compose('index', ['auth_key'=>$user->auth_key,'status'=>0])
                                ->setTo($user->email)
                                ->setFrom(['elearning.lanius@gmail.com' => 'Elearning'])
                                ->setSubject('Signup Confirmation')
                                ->send();
                        $msg['message'] = "Register success! check your e-mail for activate your account";
                        $msg['status'] = 1;
                    }else{
                        $msg['message'] = "Register failed.";
                        $msg['status'] = 0;
                    }

                }else{
                    $msg['message'] = "Register failed.";
                    $msg['status'] = 0;
                }
                return $msg;
            }else{
                $errorMsg['message'] = 'Email sudah digunakan.';
                $errorMsg['status'] = 0;
                return $errorMsg;
            }
        }else{
            $errorMsg['message'] = 'Data Tidak boleh kosong.';
            $errorMsg['status'] = 0;
            return $errorMsg;
        }
    }

    public function actionLogin()
    {
        $param=\Yii::$app->request->post();
        
        if ($param['email']!='' && $param['password']!='') {
            $data= array();
            $user = User::findByEmailAPI($param['email']);
            if ($user) {
                if ($user->account) {
                    if ($user->validatePassword($param['password'])) {
                        $user->last_login=date('Y-m-d H:i:s');
                        $user->save(false);
                        $account['login'] = Account::find()->where(['id'=>$user->account->id])->asArray()->all();
                        if ($account['login'][0]['logo']) {
                            $path = \Yii::getAlias('@backend').'/web/upload/account/'.$account['login'][0]['logo'];
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                if (file_get_contents($path)) {
                                    switch ($type) {
                                        case 'jpg':
                                        case '.jpeg':
	                                        $source_image = @imagecreatefromjpeg($path);
	                                        break;
                                        case 'gif':
                                            $source_image = @imagecreatefromgif($path);
                                            break;
                                        case 'png':
                                            $source_image = @imagecreatefrompng($path);
                                            break;
                                        case 'bmp':
                                            $source_image = @imagecreatefrombmp($path);
                                            break;
                                        default:
                                            $source_image = false;
                                            break;
                                    }

                                    if ($source_image) {
                                        $source_imagex = imagesx($source_image);
                                        $source_imagey = imagesy($source_image);
                                        $dest_imagex = 100;
                                        $dest_imagey = 100;
                                        $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
                                        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, $dest_imagey, $source_imagex, $source_imagey);
                                        // header("Content-Type: image/jpeg");
                                        // echo ;

                                        ob_start (); 

                                          imagejpeg($dest_image,NULL,80);
                                          $image_data = ob_get_contents(); 

                                        ob_end_clean ();

                                        $base64 = base64_encode($image_data);

                                    }else{
                                        $base64 = "";
                                    }                                    
                                }else{
                                    $base64 = "";
                                }
                            }else{
                                $base64 = "";
                            }
                        }else{
                            $base64 = "";
                        }

                        $account['login'][0]['base']=$base64;
                        if ($user->account->exp_date>date('Y-m-d')) {
                            $account['login'][0]['free']=0;
                        }else{
                            $account['login'][0]['free']=1;
                        }

                        $account['login'][0]['logo'] =$this->getNameFile().'/account/'.$account['login'][0]['logo'];
                        $account['login'][0]['account_id'] = $user->account->id;
                        $account['login'][0]['user_id'] = $user->id;
                        $account['login'][0]['nama_kategori'] = $account['login'][0]['id_categori_account'] ? CategoriAccount::findOne($account['login'][0]['id_categori_account'])->name : "";
                        $account['login'][0]['access_token'] = $user->auth_key;
                        $account['login'][0]['status_valid'] = 1;
                        return $account;
                    }else{
                        $data['login'][0]['status_valid']=0;
                        return $data;
                    }
                }else{
                    $data['login'][0]['status_valid']=0;
                    return $data;
                }
            }else{
                $data['login'][0]['status_valid']=0;
                return $data;
            }
        }else{
            $data['login'][0]['status_valid']=0;
            return $data;
        }
    }

    public function getNameFile(){
        $local = array(
            '127.0.0.1',
            '::1'
        );
        if (!in_array($_SERVER['REMOTE_ADDR'], $local)) {
            # code...
            // return 'http://'.$_SERVER['SERVER_ADDR'].':8088/pos-simple/backend/web/upload';

            //return 'http://'.$_SERVER['SERVER_ADDR'].'/backend/web/upload';
             //return 'http://morapos.com/pos-simple/backend/web/upload';
            return 'http://'.$_SERVER['SERVER_ADDR'].'/pos-simple/backend/web/upload';
            // return 'http://morapos.com/backend/web/upload';
        }else{
            return 'backend/web/upload';
        }
    }

}
