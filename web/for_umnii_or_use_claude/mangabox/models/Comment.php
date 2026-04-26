<?php

namespace app\models;

use yii\db\ActiveRecord;

class Comment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%comments}}';
    }

    public function rules()
    {
        return [
            [['manga_id', 'user_id', 'body'], 'required'],
            [['manga_id', 'user_id'], 'integer'],
            [['body'], 'string', 'max' => 2000],
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
