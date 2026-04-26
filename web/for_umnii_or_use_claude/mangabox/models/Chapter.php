<?php

namespace app\models;

use yii\db\ActiveRecord;

class Chapter extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%chapters}}';
    }

    public function rules()
    {
        return [
            [['manga_id', 'chapter_number'], 'required'],
            [['manga_id', 'chapter_number'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }

    public function getManga()
    {
        return $this->hasOne(Manga::class, ['id' => 'manga_id']);
    }

    public function getPages()
    {
        return $this->hasMany(ChapterPage::class, ['chapter_id' => 'id'])
            ->orderBy(['page_number' => SORT_ASC]);
    }

    public function getNextChapter()
    {
        return Chapter::find()
            ->where(['manga_id' => $this->manga_id])
            ->andWhere(['>', 'chapter_number', $this->chapter_number])
            ->orderBy(['chapter_number' => SORT_ASC])
            ->one();
    }

    public function getPrevChapter()
    {
        return Chapter::find()
            ->where(['manga_id' => $this->manga_id])
            ->andWhere(['<', 'chapter_number', $this->chapter_number])
            ->orderBy(['chapter_number' => SORT_DESC])
            ->one();
    }
}
