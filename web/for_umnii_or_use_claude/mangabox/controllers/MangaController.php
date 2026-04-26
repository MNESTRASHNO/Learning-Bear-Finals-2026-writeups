<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\models\Manga;
use app\models\Comment;
use app\models\Rating;

class MangaController extends Controller
{
    public function actionIndex()
    {
        $query = Manga::find()->orderBy(['updated_at' => SORT_DESC]);

        $status = Yii::$app->request->get('status');
        if ($status && in_array($status, ['ongoing', 'completed', 'hiatus'])) {
            $query->andWhere(['status' => $status]);
        }

        $search = Yii::$app->request->get('q');
        if ($search) {
            $query->andWhere(['or',
                ['ilike', 'title', $search],
                ['ilike', 'title_jp', $search],
                ['ilike', 'author', $search],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['mangaPerPage'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function actionView($id)
    {
        $manga = Manga::find()
            ->with(['chapters', 'uploader'])
            ->where(['id' => $id])
            ->one();

        if (!$manga) {
            throw new NotFoundHttpException('Manga not found.');
        }

        if (Yii::$app->request->isPost && !Yii::$app->user->isGuest) {
            $action = Yii::$app->request->post('action');

            if ($action === 'comment') {
                $body = trim(Yii::$app->request->post('body', ''));
                if ($body !== '') {
                    $badWords = ['fuck', 'shit', 'ass', 'bitch', 'dick', 'bastard', 'damn', 'crap', 'piss', 'slut', 'whore', 'nigger', 'faggot', 'retard'];
                    $found = null;
                    foreach ($badWords as $word) {
                        $pos = mb_stripos($body, $word);
                        if ($pos !== false) {
                            $found = $word;
                            $start = max(0, $pos - 20);
                            $length = 20 + mb_strlen($word) + 20;
                            $excerpt = mb_substr($body, $start, $length);
                            break;
                        }
                    }

                    if ($found !== null) {
                        Yii::$app->session->setFlash('error', 'Your comment contains inappropriate language: "...' . $excerpt . '..."');
                        return $this->redirect(['manga/view', 'id' => $id, '#' => 'comments']);
                    }

                    $comment = new Comment();
                    $comment->manga_id = (int) $manga->id;
                    $comment->user_id = (int) Yii::$app->user->id;
                    $comment->body = $body;
                    $comment->save();
                    Yii::$app->session->setFlash('success', 'Comment added.');
                }
                return $this->redirect(['manga/view', 'id' => $id, '#' => 'comments']);
            }

            if ($action === 'rate') {
                $score = (int) Yii::$app->request->post('score', 0);
                if ($score >= 1 && $score <= 10) {
                    $rating = Rating::findOne([
                        'manga_id' => $manga->id,
                        'user_id' => Yii::$app->user->id,
                    ]);
                    if (!$rating) {
                        $rating = new Rating();
                        $rating->manga_id = (int) $manga->id;
                        $rating->user_id = (int) Yii::$app->user->id;
                    }
                    $rating->score = $score;
                    $rating->save();
                }
                return $this->redirect(['manga/view', 'id' => $id]);
            }
        }

        $comments = Comment::find()
            ->with('user')
            ->where(['manga_id' => $manga->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $userRating = null;
        if (!Yii::$app->user->isGuest) {
            $userRating = Rating::findOne([
                'manga_id' => $manga->id,
                'user_id' => Yii::$app->user->id,
            ]);
        }

        return $this->render('view', [
            'manga' => $manga,
            'comments' => $comments,
            'userRating' => $userRating,
            'avgRating' => $manga->getAverageRating(),
            'ratingCount' => $manga->getRatingCount(),
        ]);
    }
}
