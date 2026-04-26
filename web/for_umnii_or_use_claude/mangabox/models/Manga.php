<?php

namespace app\models;

use yii\db\ActiveRecord;

class Manga extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%manga}}';
    }

    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['title_jp'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['cover_image'], 'string', 'max' => 512],
            [['author'], 'string', 'max' => 128],
            [['status'], 'in', 'range' => ['ongoing', 'completed', 'hiatus']],
            [['genres'], 'string'],
            [['uploaded_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getChapters()
    {
        return $this->hasMany(Chapter::class, ['manga_id' => 'id'])
            ->orderBy(['chapter_number' => SORT_ASC]);
    }

    public function getUploader()
    {
        return $this->hasOne(User::class, ['id' => 'uploaded_by']);
    }

    public function getLatestChapter()
    {
        return $this->hasOne(Chapter::class, ['manga_id' => 'id'])
            ->orderBy(['chapter_number' => SORT_DESC]);
    }

    public function getGenreList()
    {
        return $this->genres ? explode(',', $this->genres) : [];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['manga_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getRatings()
    {
        return $this->hasMany(Rating::class, ['manga_id' => 'id']);
    }

    public function getAverageRating()
    {
        return Rating::find()
            ->where(['manga_id' => $this->id])
            ->average('score');
    }

    public function getRatingCount()
    {
        return (int) Rating::find()
            ->where(['manga_id' => $this->id])
            ->count();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }
}
