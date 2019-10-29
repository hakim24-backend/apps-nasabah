<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $user = $this->getUser();
        $pengguna = Pengguna::find()->where(['email'=>$this->username])->one();

        if ($pengguna == null) {
            $this->addError($attribute, 'Akun Tidak Ditemukan');
        } else {
            $akun = Akun::find()->where(['id'=>$pengguna->id_akun])->one();
            $start = strtotime('09:00');
            $end = strtotime('18:00');

            if($akun->id_jenis_akun == 3 || $akun->id_jenis_akun == 4) {
                // var_dump('hai');die();
                if (!$this->hasErrors()) {
                    $user = $this->getUser();

                    if (!$user || !$user->validatePassword($this->password, $this->username)) {
                        $this->addError($attribute, 'Periksa kembali email atau password');
                    }
                }
            } elseif (time() >= $start && time() <= $end ) {
                if (!$this->hasErrors()) {
                    $user = $this->getUser();

                    if (!$user || !$user->validatePassword($this->password, $this->username)) {
                        $this->addError($attribute, 'Periksa kembali email atau password');
                    }
                }
            } else {
                if (!$this->hasErrors()) {
                    $user = $this->getUser();

                    if (!$user || !$user->validatePassword($this->password, $this->username)) {
                        $this->addError($attribute, 'Periksa kembali email atau password');
                    }
                }

                $this->addError($attribute, 'Jam bekerja belum dimulai atau sudah berakhir');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) { 
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
