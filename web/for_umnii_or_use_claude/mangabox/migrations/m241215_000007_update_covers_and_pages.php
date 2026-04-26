<?php

use yii\db\Migration;

class m241215_000007_update_covers_and_pages extends Migration
{
    public function safeUp()
    {
        for ($i = 1; $i <= 6; $i++) {
            $this->update('{{%manga}}', [
                'cover_image' => "/uploads/covers/{$i}.svg",
            ], ['id' => $i]);
        }

        $this->update('{{%chapter_pages}}', [
            'image_path' => '/uploads/dmca.svg',
        ]);

        $this->batchInsert('{{%comments}}', ['manga_id', 'user_id', 'body', 'created_at'], [
            [1, 2, 'The shadow realm arc is incredible, cant wait for the next chapter!', '2024-12-11 14:30:00'],
            [1, 2, 'Chapter 4 was a bit slow but the ending made up for it.', '2024-12-12 09:15:00'],
            [2, 2, 'This gives me major Ghost in the Shell vibes. Love it.', '2024-12-10 18:45:00'],
            [4, 2, 'The worldbuilding in this one is so detailed. Who is the human student though?', '2024-12-14 11:00:00'],
            [3, 2, 'Finished reading it. Solid ending, 8/10.', '2024-11-02 20:00:00'],
        ]);

        $this->batchInsert('{{%ratings}}', ['manga_id', 'user_id', 'score', 'created_at'], [
            [1, 2, 9, '2024-12-11 14:30:00'],
            [2, 2, 8, '2024-12-10 18:45:00'],
            [3, 2, 8, '2024-11-02 20:00:00'],
            [4, 2, 7, '2024-12-14 11:00:00'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%ratings}}');
        $this->delete('{{%comments}}');
        $this->update('{{%chapter_pages}}', ['image_path' => '/uploads/manga/1/1/1.jpg']);
        $this->update('{{%manga}}', ['cover_image' => null]);
    }
}
