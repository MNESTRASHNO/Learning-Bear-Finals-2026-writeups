<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $passwordRepeat;

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'passwordRepeat'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 6],
            [['passwordRepeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
            [['username'], 'unique', 'targetClass' => User::class, 'message' => 'This username is already taken.'],
            [['email'], 'unique', 'targetClass' => User::class, 'message' => 'This email is already registered.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'passwordRepeat' => 'Confirm Password',
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->is_admin = false;

        return $user->save() ? $user : null;
    }
}
