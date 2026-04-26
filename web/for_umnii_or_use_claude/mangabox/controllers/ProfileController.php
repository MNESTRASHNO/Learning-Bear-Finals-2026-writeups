<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\ThemeSettings;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        return $this->render('index', ['user' => $user]);
    }

    public function actionTheme()
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('action') === 'import') {
                $encoded = Yii::$app->request->post('theme_data');
                if (base64_decode($encoded, true) !== false) {
                    $user->theme_settings = $encoded;
                    $user->save(false);
                }
                return $this->redirect(['profile/theme']);
            }

            $data = Yii::$app->request->post('ThemeSettings', []);
            $theme = new ThemeSettings();
            foreach (['backgroundColor', 'textColor', 'accentColor', 'fontFamily', 'readerMode', 'fontSize'] as $attr) {
                if (isset($data[$attr])) {
                    $theme->$attr = $data[$attr];
                }
            }
            $user->theme_settings = base64_encode(serialize($theme));
            $user->save(false);
            Yii::$app->session->setFlash('success', 'Theme settings saved.');
            return $this->redirect(['profile/theme']);
        }

        $themeRaw = $user->getThemeSettings();
        $formModel = ($themeRaw instanceof ThemeSettings) ? $themeRaw : new ThemeSettings();

        return $this->render('theme', [
            'theme' => $themeRaw,
            'formModel' => $formModel,
            'exportData' => $user->theme_settings ?? base64_encode(serialize(new ThemeSettings())),
        ]);
    }
}
