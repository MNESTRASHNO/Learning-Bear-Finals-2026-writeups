<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var mixed $theme */
/** @var app\models\ThemeSettings $formModel */
/** @var string $exportData */

$this->title = 'Theme Settings';
?>

<div class="theme-page">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="theme-layout">
        <div class="theme-form-section">
            <div class="card">
                <div class="card-header">Customize</div>
                <div class="card-body">
                    <form id="theme-form" method="post">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">

                        <div class="mb-3">
                            <label class="form-label" for="ts-bg">Background Color</label>
                            <input type="color" class="form-control" id="ts-bg" name="ThemeSettings[backgroundColor]" value="<?= Html::encode($formModel->backgroundColor) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ts-text">Text Color</label>
                            <input type="color" class="form-control" id="ts-text" name="ThemeSettings[textColor]" value="<?= Html::encode($formModel->textColor) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ts-accent">Accent Color</label>
                            <input type="color" class="form-control" id="ts-accent" name="ThemeSettings[accentColor]" value="<?= Html::encode($formModel->accentColor) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ts-font">Font Family</label>
                            <select class="form-control" id="ts-font" name="ThemeSettings[fontFamily]">
                                <?php foreach (['Noto Sans', 'Noto Sans JP', 'Georgia', 'Courier New', 'Arial'] as $font): ?>
                                    <option value="<?= Html::encode($font) ?>" <?= $formModel->fontFamily === $font ? 'selected' : '' ?>><?= Html::encode($font) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reader Mode</label>
                            <div>
                                <label><input type="radio" name="ThemeSettings[readerMode]" value="scroll" <?= $formModel->readerMode === 'scroll' ? 'checked' : '' ?>> Vertical Scroll</label>
                                <label style="margin-left: 1rem;"><input type="radio" name="ThemeSettings[readerMode]" value="paged" <?= $formModel->readerMode === 'paged' ? 'checked' : '' ?>> Paged</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ts-size">Font Size (<?= Html::encode($formModel->fontSize) ?>px)</label>
                            <input type="range" class="form-control" id="ts-size" name="ThemeSettings[fontSize]" min="12" max="32" step="1" value="<?= Html::encode($formModel->fontSize) ?>">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-accent">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">Import / Export</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Export Theme</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" value="<?= Html::encode($exportData) ?>" id="export-data" readonly>
                            <button class="btn btn-outline btn-sm" type="button" onclick="navigator.clipboard.writeText(document.getElementById('export-data').value)">Copy</button>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <input type="hidden" name="action" value="import">
                        <label class="form-label">Import Theme</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" name="theme_data" placeholder="Paste theme data here...">
                            <button class="btn btn-outline btn-sm" type="submit">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="theme-preview-section">
            <div class="card">
                <div class="card-header">Preview</div>
                <div class="card-body">
                    <div class="theme-preview" style="<?= $theme ?>">
                        <div class="preview-navbar">MangaBox</div>
                        <div class="preview-content">
                            <h3>Sample Title</h3>
                            <p>This is how your reading experience will look with the current theme settings.</p>
                            <div class="preview-chapter-list">
                                <div class="preview-chapter">Chapter 1 — The Beginning</div>
                                <div class="preview-chapter">Chapter 2 — The Journey</div>
                                <div class="preview-chapter">Chapter 3 — The Revelation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
