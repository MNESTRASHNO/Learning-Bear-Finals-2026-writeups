<?php

use yii\db\Migration;

class m241215_000001_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32),
            'avatar' => $this->string(512)->null(),
            'theme_settings' => $this->text()->null(),
            'is_admin' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->createIndex('idx-users-username', '{{%users}}', 'username');
        $this->createIndex('idx-users-email', '{{%users}}', 'email');
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
