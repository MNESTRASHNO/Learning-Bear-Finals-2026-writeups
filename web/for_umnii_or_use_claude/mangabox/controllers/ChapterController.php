<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Chapter;

class ChapterController extends Controller
{
    public function actionRead($mangaId, $id)
    {
        $chapter = Chapter::find()
            ->with(['pages', 'manga'])
            ->where([
                'id' => $id,
                'manga_id' => $mangaId,
            ])
            ->one();

        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found.');
        }

        $prev = $chapter->getPrevChapter();
        $next = $chapter->getNextChapter();

        return $this->render('read', [
            'chapter' => $chapter,
            'manga' => $chapter->manga,
            'prev' => $prev,
            'next' => $next,
        ]);
    }
}
