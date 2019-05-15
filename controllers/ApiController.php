<?php

namespace app\controllers;
use yii\filters\auth\QueryParamAuth;
use app\models\Nasabah;
use app\models\NasabahBukuTelepon;
use app\models\Akun;
use Yii;

class ApiController extends \yii\rest\Controller
{
    // public function behaviors(){
    //     $behaviors = parent::behaviors();
    //     $behaviors['authenticator'] = [
    //         'class' => QueryParamAuth::className(),
    //         'except' => ['login','registrasi']
    //     ];
    //     return $behaviors;
    // }

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
        $param['ktp'] = isset($_FILES['ktp']) ? $_FILES['ktp'] : "";
        $param['with_ktp'] = isset($_FILES['with_ktp']) ? $_FILES['with_ktp'] : "";

        // var_dump($param);die;

        $response = array();

        if($param['ktp'] != "" && $param['with_ktp'] != "" && $param['sex'] != "" && $param['email'] != "" && $param['phone_number'] != "" && $param['name'] != "" && $param['password'] != "" && $param['address'] != "" && $param['birth_place'] != "" && $param['birth_date'] != ""){

            $nasabah = Nasabah::find()->where(['email' => $param['email']])->one();

            if($nasabah == null){
            	$transaction = Yii::$app->db->beginTransaction();
                try{
	                $akun = new Akun();
	                $akun_id = $akun->createAkun($akun, $param['password'], 2);

	                if($akun_id){

	                	$nasabah = new Nasabah();
	                	$nasabah->id_akun = $akun_id;
	                	$nasabah->nama = $param['name'];
	                	$nasabah->alamat = $param['address'];
	                	$nasabah->tempat_lahir = $param['birth_place'];
	                	$nasabah->tanggal_lahir = $param['birth_date'];
	                	$nasabah->jenis_kelamin = $param['sex'];
	                	$nasabah->email = $param['email'];
	                	$nasabah->nomor_telepon = $param['phone_number'];

	                    if($nasabah->save(false)){

	                    	$extension = $this->getFileExtension($param['ktp']['name']);
	                        $name = "ktp-".$nasabah->id."-".time(). '.' . $extension;
	                        $filedest = 'foto/' . $name;
	                        move_uploaded_file($param['ktp']['tmp_name'], $filedest);
	                        $nasabah->foto_ktp = $name;

	                        $extension = $this->getFileExtension($param['with_ktp']['name']);
	                        $name = "with_ktp-".$nasabah->id."-".time(). '.' . $extension;
	                        $filedest = 'foto/' . $name;
	                        move_uploaded_file($param['with_ktp']['tmp_name'], $filedest);
	                        $nasabah->foto_bersama_ktp = $name;

	                        $nasabah->save(false);

	                        $response['message'] = "Registrasi berhasil";
	                        $response['status'] = 1;
	                        $response['customer_id'] = $nasabah->id;
	                        $transaction->commit();
	                    }
	                }

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $response['message'] = $e->getMessage();
                    $response['status'] = 0;
                }
            }else{
                $response['message'] = 'Email sudah digunakan';
                $response['status'] = 0;
            }
        }else{
            $response['message'] = 'Data Tidak boleh kosong';
            $response['status'] = 0;
        }

        return $response;
    }

    public function actionLogin()
    {
        $param=\Yii::$app->request->post();

        $response= array();
        if ($param['email']!='' && $param['password']!='') {
            
            $nasabah = Nasabah::findByEmailAPI($param['email']);
            if ($nasabah) {
                if ($nasabah->akun) {
                    if ($nasabah->validatePassword($param['password'])) {
                        $response['message'] = 'Berhasil login';
            			$response['status'] = 1;
                    }else{
                        $response['message'] = 'Password tidak sesuai';
            			$response['status'] = 0;
                    }
                }else{
                    $response['message'] = 'Akun tidak ditemukan';
            		$response['status'] = 0;
                }
            }else{
                $response['message'] = 'Nasabah tidak ditemukan';
            	$response['status'] = 0;
            }
        }else{
            $response['message'] = 'Data Tidak boleh kosong';
            $response['status'] = 0;
        }

        return $response;
    }

    public function actionSendContacts(){
        $param = Yii::$app->request->post();
        $response = array();

        $request = json_decode($param['contacts'], true);

        if (isset($request)) {
        	if ($request['customer_id'] != "") {
        		$transaction = Yii::$app->db->beginTransaction();
                try{
                	foreach ($request['phone_numbers'] as $key => $value) {
                		$phone_number = new NasabahBukuTelepon();
                		$phone_number->nama = $value['nama'];
                		$phone_number->nomor_telepon = $value['nomor_telepon'];
                		$phone_number->id_nasabah = $request['customer_id'];

                		$phone_number->save(false);
                	}

                	$transaction->commit();
                	$response['message'] = 'Berhasil mengirim kontak';
            		$response['status'] = 1;
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $response['message'] = $e->getMessage();
                    $response['status'] = 0;
                }
        	} else {
        		$response['message'] = 'Data Tidak boleh kosong';
            	$response['status'] = 0;
        	}
        } else {
        	$response['message'] = 'Data Tidak boleh kosong';
            $response['status'] = 0;
        }

        return $response; 
    }

    function getFileExtension($file)
    {
        $path_parts = pathinfo($file);
        return $path_parts['extension'];
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
