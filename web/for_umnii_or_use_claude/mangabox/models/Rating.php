<?php

namespace app\models;

use yii\db\ActiveRecord;

class Rating extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ratings}}';
    }

    public function rules()
    {
        return [
            [['manga_id', 'user_id', 'score'], 'required'],
            [['manga_id', 'user_id'], 'integer'],
            [['score'], 'integer', 'min' => 1, 'max' => 10],
            [['created_at'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getManga()
    {
        return $this->hasOne(Manga::class, ['id' => 'manga_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
}
