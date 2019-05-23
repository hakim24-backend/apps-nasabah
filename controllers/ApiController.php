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
use app\models\PencicilanJenis;
use app\models\PencicilanStatusBayar;
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
	                $akun = $akun->createAkun($akun, $param['password'], 2);

	                if($akun->id){

	                	$nasabah = new Nasabah();
	                	$nasabah->id_akun = $akun->id;
	                	$nasabah->nama = $param['name'];
	                	$nasabah->alamat = $param['address'];
	                	$nasabah->tempat_lahir = $param['birth_place'];
	                	$nasabah->tanggal_lahir = $param['birth_date'];
	                	$nasabah->jenis_kelamin = $param['sex'];
	                	$nasabah->email = $param['email'];
	                	$nasabah->nomor_telepon = $param['phone_number'];

	                    if($nasabah->save(false)){

	                        if($nasabah->save(false)){

								$email = \Yii::$app->mailer->compose('index', ['access_token'=>$akun->access_token])
	                                ->setTo($nasabah->email)
	                                ->setFrom(['mamorasoft.firebase@gmail.com'])
	                                ->setSubject('Konfirmasi Pendaftaran')
	                                ->send();

	                            if ($email){
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
		                    	} else {
		                    		$transaction->rollBack();
				                    $response['message'] = "Gagal mengirim email konfirmasi";
				                    $response['status'] = 0;
		                    	}
		                    }
                           
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

    public function actionSendLocation(){
        $request = Yii::$app->request->post();
        $response = array();

        if (isset($request)) {
        	if ($request['customer_id'] != "" && $request['latitude'] != "" && $request['longitude'] != "") {
        		$transaction = Yii::$app->db->beginTransaction();
                try{
                	$nasabah = Nasabah::find()->where(['id'=>$request['customer_id']])->one();
                	$nasabah->latitude = $request['latitude'];
                	$nasabah->longitude = $request['longitude'];
                	$nasabah->tanggal_waktu_posisi = date('Y-m-d H:i:s');
                	$nasabah->save(false);

                	$transaction->commit();
                	$response['message'] = 'Berhasil mengirim lokasi';
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

            	$credit = Peminjaman::find()->where(['id_nasabah'=>$param['customer_id']])->orderBy(['tanggal_waktu_pembuatan'=>SORT_DESC])->asArray()->all();

	        	if ($credit) {

	        		foreach ($credit as $key => $value) {

	        			//tanggal_peminjaman
	        			$date=date_create($value['tanggal_waktu_pembuatan']);
	        			$value['tanggal_waktu_pembuatan'] = date_format($date, 'd F Y');

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
							//sisa_kali_pencicilan
	        				$value['sisa_kali_pembayaran'] = 0;
	        			} else {
							//sisa_kali_pencicilan
							$paid_count = Pencicilan::find()->where(['id_peminjaman'=>$value['id']])->andWhere(['id_status_bayar'=>2])->count();
	        				$difference = $value['durasi'] - $paid_count;
	        				$value['sisa_kali_pembayaran'] = $difference;
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

    public function actionCreditView(){

		$param = Yii::$app->request->get();
        $response = array();

    	if ($param['credit_id']!="") {

        	$credit = Peminjaman::find()->where(['id'=>$param['credit_id']])->asArray()->one();

        	if ($credit) {

        		$last_amount = 0;
        		$amount_left = 0;
        		$get_late_penalty = 0;
        		$current_period = 0;
    			$bills = Pencicilan::find()->where(['id_peminjaman'=>$credit['id']])->orderBy(['periode'=>SORT_ASC])->asArray()->all();
    			$peminjaman_jenis = PeminjamanJenis::find()->where(['id'=>$credit['id_jenis_peminjaman']])->asArray()->one();

    			foreach ($bills as $key => $value) {
					//denda
					if($value['id_status_bayar'] == 1) {
	        			// $value['nominal_denda'] = Peminjaman::getDenda($credit['id_jenis_durasi'], $value['tanggal_jatuh_tempo'], $credit['nominal_pencicilan'], $peminjaman_jenis['besar_denda']);
	        			$value['nominal_denda'] = Peminjaman::getDenda($value['tanggal_jatuh_tempo'], $credit['nominal_pencicilan'], $peminjaman_jenis['besar_denda']);
	        		} else {
	        			if($value['nominal_denda_dibayar'] != null && $value['nominal_denda_dibayar'] > 0 ){
		        			$value['nominal_denda'] = $value['nominal_denda_dibayar'];
		        		} else {
		        			$value['nominal_denda'] = 0;
		        		}
	        		}

    				//nama_pelayan
        			$pengguna = Pengguna::find()->where(['id'=>$value['id_pengguna']])->one();
        			$value['pengguna'] = $pengguna['nama'];

        			//status_bayar
        			$status_bayar = PencicilanStatusBayar::find()->where(['id'=>$value['id_status_bayar']])->one();
        			$value['status_bayar'] = $status_bayar['status_bayar'];

        			//jenis_pencicilan
        			$jenis_pencicilan = PencicilanJenis::find()->where(['id'=>$value['id_jenis_pencicilan']])->one();
        			$value['jenis_pencicilan'] = $jenis_pencicilan['jenis_pencicilan'];

        			//tanggal_peminjaman
        			$date_due=date_create($value['tanggal_jatuh_tempo']);
        			$value['tanggal_jatuh_tempo'] = date_format($date_due, 'd F Y');

        			//tanggal_waktu_cicilan
        			if ($value['tanggal_waktu_cicilan']){
        				$date_payment=date_create($value['tanggal_waktu_cicilan']);
        				$value['tanggal_waktu_cicilan'] = date_format($date_payment, 'd F Y');
        			}

        			$bills[$key] = $value;

        			//pre_pelunasan_calculation
        			// if($credit['id_status_peminjaman'] == 1){
        			// 	if($credit['id_jenis_peminjaman'] == 1){
		        	// 		if($current_period == 0){
		        	// 			if($value['id_status_bayar'] == 1){
		        	// 				$last_amount += ($credit['nominal_pencicilan'] + $value['nominal_denda']);
		        	// 			}
		        	// 			if(strtotime(date("Y-m-d")) < strtotime($value['tanggal_jatuh_tempo'])){
		        	// 				$current_period = 1;
			        // 			}
			        // 		} else {
			        // 			$amount_left += ($credit['nominal_peminjaman'] / $credit['durasi']);
			        // 		}
			        // 	} else {
			        // 		if($current_period == 0){
		        	// 			if($value['id_status_bayar'] == 1){
		        	// 				$last_amount += ($credit['nominal_pencicilan'] + $value['nominal_denda']);
		        	// 				if($value['nominal_denda'] > 0){
		        	// 					$get_late_penalty = 1;
		        	// 				}
		        	// 			} else {
		        	// 				if($value['nominal_denda_dibayar'] != null && $value['nominal_denda_dibayar'] > 0 ){
		        	// 					$get_late_penalty = 1;
		        	// 				}
		        	// 			}
		        	// 			if(strtotime(date("Y-m-d")) < strtotime($value['tanggal_jatuh_tempo'])){
		        	// 				$current_period = 1;
			        // 			}
			        // 		} else {
			        // 			$amount_left += $credit['nominal_pencicilan'];
			        // 		}
			        // 	}
		        	// }
    			}

    			// //final_pelunasan_calculation
    			// if($credit['id_status_peminjaman'] == 1){
	    		// 	if($credit['id_jenis_peminjaman'] == 1){
		    	// 		$response['direct_payment_amount'] = $last_amount + $amount_left + $amount_left * $peminjaman_jenis['besar_pinalti_langsung_lunas'];
		    	// 	} else {
		    	// 		$response['direct_payment_amount'] = $last_amount + $amount_left;
		    	// 		if ($get_late_penalty == 0){
			    // 			$response['direct_payment_amount'] -= $credit['nominal_tabungan_ditahan'];
			    // 		}
		    	// 	}
		    	// } else {
		    	// 	$response['direct_payment_amount'] = 0;
		    	// }

    			
	            //lunas dipercepat jamina	
    			$totalCicilan = Pencicilan::getTotalCicilan($credit['id']);
	            $response['direct_payment_amount'] = Pencicilan::getLunasDipercepat($credit['id_jenis_peminjaman'], $totalCicilan, $credit['durasi'], $credit['nominal_peminjaman'], $peminjaman_jenis['besar_pinalti_langsung_lunas']);
		        
        		$response['bill'] = $bills;
	        	$response['message'] = 'Berhasil mengambil data';
	            $response['status'] = 1;
	        } else {
	        	$response['message'] = 'Peminjaman tidak ditemukan';
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
