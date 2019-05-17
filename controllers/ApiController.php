<?php

namespace app\controllers;
use yii\filters\auth\QueryParamAuth;
use app\models\Nasabah;
use app\models\NasabahBukuTelepon;
use app\models\Akun;
use app\models\Peminjaman;
use app\models\PeminjamanDurasiJenis;
use app\models\PeminjamanJenis;
use app\models\PeminjamanStatus;
use app\models\Pengguna;
use app\models\Pencicilan;
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
            $response['message'] = 'Data tidak boleh kosong';
            $response['status'] = 0;
        }

        return $response;
    }

    public function actionLogin()
    {
        $param=\Yii::$app->request->post();

        $response= array();
        if ($param['email']!="" && $param['password']!="") {
            
            $nasabah = Nasabah::find()->where(['email'=>$param['email']])->one();
            if ($nasabah) {
            	$akun = Akun::find()->where(['id'=>$nasabah->id_akun])->one();
            	if ($akun->id_status_akun == 1) {
	            	if ($nasabah->validatePassword($param['password'], $akun->password_hash)) {
	            		$response['id'] = $nasabah->id;
	        			$response['nama'] = $nasabah->nama;
	        			$response['email'] = $nasabah->email;
	        			$response['nomor_telepon'] = $nasabah->nomor_telepon;
	        			$response['access_token'] = $akun->access_token;
	                    $response['message'] = 'Berhasil login';
	        			$response['status'] = 1;
	                }else{
	                    $response['message'] = 'Password tidak sesuai';
	        			$response['status'] = 0;
	                }
	            } else {
	            	$response['message'] = 'Akun tidak aktif';
	        		$response['status'] = 0;
	            }
            }else{
                $response['message'] = 'Email tidak ditemukan';
            	$response['status'] = 0;
            }
        }else{
            $response['message'] = 'Data tidak boleh kosong';
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
                	$response['message'] = 'Berhasil mendaftar';
            		$response['status'] = 1;
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $response['message'] = $e->getMessage();
                    $response['status'] = 0;
                }
        	} else {
        		$response['message'] = 'Data tidak boleh kosong';
            	$response['status'] = 0;
        	}
        } else {
        	$response['message'] = 'Data tidak boleh kosong';
            $response['status'] = 0;
        }

        return $response; 
    }

    public function actionCreditList(){
        $param = Yii::$app->request->get();
        $response = array();

        if ($param['customer_id']!="") {

        	$customer = Nasabah::find()->where(['id'=>$param['customer_id']])->one();

            if ($customer) {

            	$credit = Peminjaman::find()->where(['id_nasabah'=>$param['customer_id']])->asArray()->all();

	        	if ($credit) {

	        		foreach ($credit as $key => $value) {

	        			//jenis_peminjaman
	        			$date=date_create($value['tanggal_waktu_pembuatan']);
	        			$value['tanggal_waktu_pembuatan'] = date_format($date, 'd M Y');

	        			//jenis_peminjaman
	        			$jenis_peminjaman = PeminjamanJenis::find()->where(['id'=>$value['id_jenis_peminjaman']])->one();
	        			$value['jenis_peminjaman'] = $jenis_peminjaman->jenis_peminjaman;

	        			//jenis_durasi_peminjaman
	        			$jenis_durasi = PeminjamanDurasiJenis::find()->where(['id'=>$value['id_jenis_durasi']])->one();
	        			$value['jenis_durasi'] = $jenis_durasi->durasi_peminjaman;

	        			//status_peminjaman
	        			$status_peminjaman = PeminjamanStatus::find()->where(['id'=>$value['id_status_peminjaman']])->one();
	        			$value['status_peminjaman'] = $status_peminjaman->status_peminjaman;

	        			//nama_pelayan
	        			$pengguna = Pengguna::find()->where(['id'=>$value['id_pengguna']])->one();
	        			$value['pengguna'] = $pengguna['nama'];

	        			if($value['id_status_peminjaman'] == 2){
	        				//denda
	        				$value['denda'] = 0;

	        				//sisa_pencicilan
	        				$value['payment_count_left'] = 0;
	        			} else {
							//sisa_pencicilan
							$paid_count = Pencicilan::find()->where(['id_peminjaman'=>$value['id']])->count();
	        				$difference = $value['durasi'] - $paid_count;
	        				$value['sisa_kali_pembayaran'] = $difference;

	        				//denda
	        				$value['denda'] = Peminjaman::getDenda($value['tanggal_waktu_pembuatan'], $paid_count, $value['nominal_pencicilan'], $jenis_peminjaman->besar_denda);
	        			}

	        			$credit[$key] = $value;
	        		}

	        		$response['credit'] = $credit;
		        	$response['message'] = 'Berhasil mengambil data';
		            $response['status'] = 1;
		        } else {
		        	$response['message'] = 'Peminjaman tidak ditemukan';
		            $response['status'] = 0;
		        }
	        } else {
	        	$response['message'] = 'Nasabah tidak ditemukan';
	            $response['status'] = 0;
	        }
        } else {
        	$response['message'] = 'Data tidak boleh kosong';
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
