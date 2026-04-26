<?php

use yii\db\Migration;

class m241215_000004_create_chapter_page_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chapter_pages}}', [
            'id' => $this->primaryKey(),
            'chapter_id' => $this->integer()->notNull(),
            'page_number' => $this->integer()->notNull(),
            'image_path' => $this->string(512)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-chapter_pages-chapter_id',
            '{{%chapter_pages}}',
            'chapter_id',
            '{{%chapters}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-chapter_pages-chapter_id', '{{%chapter_pages}}', 'chapter_id');
        $this->createIndex('idx-chapter_pages-order', '{{%chapter_pages}}', ['chapter_id', 'page_number'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%chapter_pages}}');
    }
}
