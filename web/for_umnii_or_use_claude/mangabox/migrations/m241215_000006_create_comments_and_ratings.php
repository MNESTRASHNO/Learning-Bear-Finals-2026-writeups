<?php

use yii\db\Migration;

class m241215_000006_create_comments_and_ratings extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%comments}}', [
            'id' => $this->primaryKey(),
            'manga_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'body' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey('fk-comments-manga_id', '{{%comments}}', 'manga_id', '{{%manga}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-comments-user_id', '{{%comments}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->createIndex('idx-comments-manga_id', '{{%comments}}', 'manga_id');

        $this->createTable('{{%ratings}}', [
            'id' => $this->primaryKey(),
            'manga_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'score' => $this->smallInteger()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey('fk-ratings-manga_id', '{{%ratings}}', 'manga_id', '{{%manga}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-ratings-user_id', '{{%ratings}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->createIndex('idx-ratings-unique', '{{%ratings}}', ['manga_id', 'user_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ratings}}');
        $this->dropTable('{{%comments}}');
    }
}
