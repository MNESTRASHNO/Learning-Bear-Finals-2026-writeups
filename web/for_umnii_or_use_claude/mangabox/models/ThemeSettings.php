<?php

namespace app\models;

class ThemeSettings
{
    public $backgroundColor = '#0f0f1a';
    public $textColor = '#e0e0e0';
    public $accentColor = '#ff6b9d';
    public $fontFamily = 'Noto Sans';
    public $readerMode = 'scroll';
    public $fontSize = 16;

    public function __toString()
    {
        return sprintf(
            '--mg-bg: %s; --mg-text: %s; --mg-accent: %s; --mg-font: %s; --mg-font-size: %dpx;',
            $this->backgroundColor,
            $this->textColor,
            $this->accentColor,
            $this->fontFamily,
            $this->fontSize
        );
    }
}
