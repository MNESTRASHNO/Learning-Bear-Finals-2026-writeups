<?php

use yii\db\Migration;

class m241215_000002_create_manga_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%manga}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'title_jp' => $this->string(255)->null(),
            'description' => $this->text()->notNull(),
            'cover_image' => $this->string(512)->null(),
            'author' => $this->string(128)->null(),
            'status' => $this->string(20)->notNull()->defaultValue('ongoing'),
            'genres' => $this->text()->null(),
            'uploaded_by' => $this->integer()->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-manga-uploaded_by',
            '{{%manga}}',
            'uploaded_by',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('idx-manga-status', '{{%manga}}', 'status');
        $this->createIndex('idx-manga-updated_at', '{{%manga}}', 'updated_at');
    }

    public function safeDown()
    {
        $this->dropTable('{{%manga}}');
    }
}
