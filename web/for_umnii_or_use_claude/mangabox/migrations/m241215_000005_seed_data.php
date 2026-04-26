<?php

use yii\db\Migration;

class m241215_000005_seed_data extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'email' => 'admin@mangabox.local',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'is_admin' => true,
            'created_at' => '2024-06-01 00:00:00',
            'updated_at' => '2024-12-15 10:00:00',
        ]);

        $this->insert('{{%users}}', [
            'username' => 'reader',
            'email' => 'reader@mangabox.local',
            'password_hash' => Yii::$app->security->generatePasswordHash('reader123'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'is_admin' => false,
            'created_at' => '2024-08-15 00:00:00',
            'updated_at' => '2024-12-15 10:00:00',
        ]);

        $manga = [
            [
                'title' => 'Shadow Blade Chronicles',
                'title_jp' => "\xe5\xbd\xb1\xe5\x88\x83\xe4\xbc\x9d\xe8\xaa\xac",
                'description' => 'In a world where shadows come alive, a young swordsman discovers an ancient blade that grants him power over darkness. But as he delves deeper into the shadow realm, he realizes the blade has a will of its own.',
                'author' => 'Tanaka Hiroshi',
                'status' => 'ongoing',
                'genres' => 'Action,Fantasy,Adventure',
                'uploaded_by' => 1,
                'created_at' => '2024-07-10 12:00:00',
                'updated_at' => '2024-12-10 08:00:00',
            ],
            [
                'title' => 'Neon District',
                'title_jp' => "\xe3\x83\x8d\xe3\x82\xaa\xe3\x83\xb3\xe5\x9c\xb0\xe5\x8c\xba",
                'description' => 'Set in a cyberpunk metropolis, a group of hackers uncovers a conspiracy that reaches the highest levels of corporate governance. Their only weapon: a mysterious AI that seems to predict the future.',
                'author' => 'Yamamoto Kenji',
                'status' => 'ongoing',
                'genres' => 'Sci-Fi,Thriller,Cyberpunk',
                'uploaded_by' => 1,
                'created_at' => '2024-08-05 12:00:00',
                'updated_at' => '2024-12-12 09:00:00',
            ],
            [
                'title' => 'Kitchen Wars',
                'title_jp' => "\xe5\x8e\xa8\xe6\x88\xbf\xe6\x88\xa6\xe4\xba\x89",
                'description' => 'A culinary battle manga where aspiring chefs compete in increasingly impossible cooking challenges. The secret ingredient? A dash of magic in every recipe.',
                'author' => 'Suzuki Aoi',
                'status' => 'completed',
                'genres' => 'Comedy,Cooking,Slice of Life',
                'uploaded_by' => 1,
                'created_at' => '2024-06-15 12:00:00',
                'updated_at' => '2024-11-01 10:00:00',
            ],
            [
                'title' => 'Moonlit Academy',
                'title_jp' => "\xe6\x9c\x88\xe5\x85\x89\xe5\xad\xa6\xe5\x9c\x92",
                'description' => 'At a prestigious academy hidden from human eyes, supernatural students learn to control their abilities. When a human accidentally enrolls, the balance between worlds begins to crumble.',
                'author' => 'Nakamura Yuki',
                'status' => 'ongoing',
                'genres' => 'Fantasy,Romance,School',
                'uploaded_by' => 1,
                'created_at' => '2024-09-01 12:00:00',
                'updated_at' => '2024-12-14 07:00:00',
            ],
            [
                'title' => 'Drift Protocol',
                'title_jp' => "\xe3\x83\x89\xe3\x83\xaa\xe3\x83\x95\xe3\x83\x88\xe3\x83\x97\xe3\x83\xad\xe3\x83\x88\xe3\x82\xb3\xe3\x83\xab",
                'description' => 'Underground racing meets mech combat. Pilots drift giant mechas through neon-lit streets in illegal races that could reshape the power structure of the entire colony.',
                'author' => 'Ito Ren',
                'status' => 'hiatus',
                'genres' => 'Mecha,Racing,Action',
                'uploaded_by' => 1,
                'created_at' => '2024-10-20 12:00:00',
                'updated_at' => '2024-11-15 11:00:00',
            ],
            [
                'title' => 'The Last Pharmacist',
                'title_jp' => "\xe6\x9c\x80\xe5\xbe\x8c\xe3\x81\xae\xe8\x96\xac\xe5\x89\xa4\xe5\xb8\xab",
                'description' => 'In a post-apocalyptic world where medicine is the most valuable currency, the last trained pharmacist holds the key to humanity\'s survival. But everyone wants what she knows.',
                'author' => 'Watanabe Mai',
                'status' => 'ongoing',
                'genres' => 'Drama,Post-Apocalyptic,Medical',
                'uploaded_by' => 1,
                'created_at' => '2024-11-01 12:00:00',
                'updated_at' => '2024-12-13 06:00:00',
            ],
        ];

        foreach ($manga as $m) {
            $this->insert('{{%manga}}', $m);
        }

        $chapters = [
            ['manga_id' => 1, 'chapter_number' => 1, 'title' => 'The Blade Awakens', 'created_at' => '2024-07-10 12:00:00'],
            ['manga_id' => 1, 'chapter_number' => 2, 'title' => 'Into the Shadow Realm', 'created_at' => '2024-07-24 12:00:00'],
            ['manga_id' => 1, 'chapter_number' => 3, 'title' => 'First Blood', 'created_at' => '2024-08-07 12:00:00'],
            ['manga_id' => 1, 'chapter_number' => 4, 'title' => 'The Dark Tournament', 'created_at' => '2024-08-21 12:00:00'],
            ['manga_id' => 2, 'chapter_number' => 1, 'title' => 'Boot Sequence', 'created_at' => '2024-08-05 12:00:00'],
            ['manga_id' => 2, 'chapter_number' => 2, 'title' => 'Ghost in the Wire', 'created_at' => '2024-08-19 12:00:00'],
            ['manga_id' => 2, 'chapter_number' => 3, 'title' => 'Firewall', 'created_at' => '2024-09-02 12:00:00'],
            ['manga_id' => 3, 'chapter_number' => 1, 'title' => 'The First Dish', 'created_at' => '2024-06-15 12:00:00'],
            ['manga_id' => 3, 'chapter_number' => 2, 'title' => 'Spice and Spirit', 'created_at' => '2024-06-29 12:00:00'],
            ['manga_id' => 4, 'chapter_number' => 1, 'title' => 'Enrollment', 'created_at' => '2024-09-01 12:00:00'],
            ['manga_id' => 4, 'chapter_number' => 2, 'title' => 'The Hidden Floor', 'created_at' => '2024-09-15 12:00:00'],
            ['manga_id' => 4, 'chapter_number' => 3, 'title' => 'Moonrise', 'created_at' => '2024-09-29 12:00:00'],
            ['manga_id' => 5, 'chapter_number' => 1, 'title' => 'Ignition', 'created_at' => '2024-10-20 12:00:00'],
            ['manga_id' => 6, 'chapter_number' => 1, 'title' => 'The Last Prescription', 'created_at' => '2024-11-01 12:00:00'],
            ['manga_id' => 6, 'chapter_number' => 2, 'title' => 'Supply and Demand', 'created_at' => '2024-11-15 12:00:00'],
        ];

        foreach ($chapters as $ch) {
            $this->insert('{{%chapters}}', $ch);
        }

        $pages = [];
        for ($i = 1; $i <= 15; $i++) {
            $chapterId = $chapters[$i - 1]['manga_id'];
            for ($p = 1; $p <= 8; $p++) {
                $pages[] = [
                    'chapter_id' => $i,
                    'page_number' => $p,
                    'image_path' => "/uploads/manga/{$chapters[$i-1]['manga_id']}/{$chapters[$i-1]['chapter_number']}/{$p}.jpg",
                ];
            }
        }

        foreach ($pages as $page) {
            $this->insert('{{%chapter_pages}}', $page);
        }
    }

    public function safeDown()
    {
        $this->delete('{{%chapter_pages}}');
        $this->delete('{{%chapters}}');
        $this->delete('{{%manga}}');
        $this->delete('{{%users}}');
    }
}
