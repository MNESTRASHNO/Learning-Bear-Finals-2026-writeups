<?php

use yii\db\Migration;

class m241215_000003_create_chapter_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chapters}}', [
            'id' => $this->primaryKey(),
            'manga_id' => $this->integer()->notNull(),
            'chapter_number' => $this->integer()->notNull(),
            'title' => $this->string(255)->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-chapters-manga_id',
            '{{%chapters}}',
            'manga_id',
            '{{%manga}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-chapters-manga_id', '{{%chapters}}', 'manga_id');
        $this->createIndex('idx-chapters-number', '{{%chapters}}', ['manga_id', 'chapter_number'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%chapters}}');
    }
}
