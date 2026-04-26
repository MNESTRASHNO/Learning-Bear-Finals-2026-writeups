<?php

namespace app\models;

use yii\db\ActiveRecord;

class ChapterPage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%chapter_pages}}';
    }

    public function rules()
    {
        return [
            [['chapter_id', 'page_number', 'image_path'], 'required'],
            [['chapter_id', 'page_number'], 'integer'],
            [['image_path'], 'string', 'max' => 512],
        ];
    }

    public function getChapter()
    {
        return $this->hasOne(Chapter::class, ['id' => 'chapter_id']);
    }
}
