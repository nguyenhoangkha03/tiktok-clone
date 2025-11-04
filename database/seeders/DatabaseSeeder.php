<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        DB::table('video_views')->truncate();
        DB::table('video_reports')->truncate();
        DB::table('not_interested')->truncate();
        DB::table('live_chats')->truncate();
        DB::table('live_streams')->truncate();
        DB::table('notifications')->truncate();
        DB::table('reports')->truncate();
        DB::table('blocks')->truncate();
        DB::table('comment_likes')->truncate();
        DB::table('messages')->truncate();
        DB::table('favorites')->truncate();
        DB::table('comments')->truncate();
        DB::table('likes')->truncate();
        DB::table('videos')->truncate();
        DB::table('follows')->truncate();
        DB::table('users')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed Users
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Kha',
                'username' => 'akaisui',
                'email' => 'akaisui03@gmail.com',
                'avatar' => 'avatars/1760580348_1.jpg',
                'bio' => 'Follow to see more',
                'website' => 'https://akaisui.vercel.app/',
                'instagram' => 'hoangkha1910',
                'youtube' => 'hanhlanganime03',
                'facebook' => 'NguyenHoangKha1910',
                'twitter' => 'akaisui03',
                'email_verified_at' => null,
                'password' => '$2y$12$ermGhkuaHA3iCgObrQQvWeZj1ZDWr5m9PZYtAn1wurzMCTEXOdjGK',
                'remember_token' => null,
                'created_at' => '2025-10-13 18:34:10',
                'updated_at' => '2025-10-19 01:29:13',
            ],
            [
                'id' => 2,
                'name' => 'Lê Minh Khang',
                'username' => 'minhkhang',
                'email' => 'khang@gmail.com',
                'avatar' => 'avatars/1760582858_2.png',
                'bio' => 'Tôi là best Zata all time',
                'website' => null,
                'instagram' => null,
                'youtube' => null,
                'facebook' => null,
                'twitter' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$U5XCBqFC32RIFf92Mqd17OYxU4FTYXICau/FYIlRNl.2MIK1cE8f2',
                'remember_token' => null,
                'created_at' => '2025-10-15 02:38:09',
                'updated_at' => '2025-10-15 19:47:38',
            ],
            [
                'id' => 3,
                'name' => 'Nào có vàng đơn đổi tên ??',
                'username' => 'ng_minhqun',
                'email' => 'ng_minhqun@gmail.com',
                'avatar' => 'avatars/1760583530_3.jpeg',
                'bio' => 'I love Momota',
                'website' => null,
                'instagram' => null,
                'youtube' => null,
                'facebook' => null,
                'twitter' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$a4FtvtAbol9NrZKuRiHFmOgXBC8I5QFtG46koEOPJD1iSYXXCGoIe',
                'remember_token' => null,
                'created_at' => '2025-10-15 19:57:26',
                'updated_at' => '2025-10-15 19:58:50',
            ],
        ]);

        // Seed Videos
        DB::table('videos')->insert([
            [
                'id' => 2,
                'user_id' => 1,
                'title' => 'Cristiano',
                'description' => 'Cristiano',
                'video_path' => 'videos/1760517757_1.mp4',
                'thumbnail' => null,
                'views' => 6,
                'created_at' => '2025-10-15 01:42:37',
                'updated_at' => '2025-10-17 06:19:36',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'title' => 'Nỗi oan của Lữ Bố',
                'description' => '#lubo #noioan',
                'video_path' => 'videos/1760520646_1.mp4',
                'thumbnail' => null,
                'views' => 27,
                'created_at' => '2025-10-15 02:30:46',
                'updated_at' => '2025-10-18 07:05:27',
            ],
            [
                'id' => 4,
                'user_id' => 2,
                'title' => 'Cô gái Trung Quốc hát hay',
                'description' => '#trungquoc #hat #douyin',
                'video_path' => 'videos/1760521202_2.mp4',
                'thumbnail' => null,
                'views' => 7,
                'created_at' => '2025-10-15 02:40:02',
                'updated_at' => '2025-10-19 00:22:21',
            ],
            [
                'id' => 5,
                'user_id' => 3,
                'title' => 'Anh chi muon... ?',
                'description' => '#cầulông #xuhuong #abcxyz #kentomomota',
                'video_path' => 'videos/1760583629_3.mp4',
                'thumbnail' => 'thumbnails/1760583629_3_thumb.jpg',
                'views' => 25,
                'created_at' => '2025-10-15 20:00:29',
                'updated_at' => '2025-10-17 06:07:34',
            ],
            [
                'id' => 6,
                'user_id' => 3,
                'title' => 'Tài hay xỉu ?',
                'description' => '#cầulông #viral #xuhuong #abcxyz #kentomomota #viktoraxelsen',
                'video_path' => 'videos/1760583740_3.mp4',
                'thumbnail' => 'thumbnails/1760583740_3_thumb.jpg',
                'views' => 30,
                'created_at' => '2025-10-15 20:02:20',
                'updated_at' => '2025-10-19 00:13:16',
            ],
            [
                'id' => 7,
                'user_id' => 3,
                'title' => 'Đăng nhanh ko mốc ?',
                'description' => '#cầulông #viral #xuhuong #abcxyz #kentomomota #viktoraxelsen',
                'video_path' => 'videos/1760583854_3.mp4',
                'thumbnail' => 'thumbnails/1760583854_3_thumb.jpg',
                'views' => 196,
                'created_at' => '2025-10-15 20:04:14',
                'updated_at' => '2025-10-19 01:29:57',
            ],
        ]);

        // Seed Follows
        DB::table('follows')->insert([
            ['id' => 7, 'follower_id' => 1, 'following_id' => 3, 'created_at' => '2025-10-19 00:14:20', 'updated_at' => '2025-10-19 00:14:20'],
            ['id' => 8, 'follower_id' => 1, 'following_id' => 2, 'created_at' => '2025-10-19 00:22:10', 'updated_at' => '2025-10-19 00:22:10'],
            ['id' => 9, 'follower_id' => 2, 'following_id' => 1, 'created_at' => '2025-10-19 00:47:50', 'updated_at' => '2025-10-19 00:47:50'],
        ]);

        // Seed Likes
        DB::table('likes')->insert([
            ['id' => 2, 'user_id' => 1, 'video_id' => 3, 'created_at' => '2025-10-15 02:32:20', 'updated_at' => '2025-10-15 02:32:20'],
            ['id' => 4, 'user_id' => 1, 'video_id' => 7, 'created_at' => '2025-10-15 23:44:46', 'updated_at' => '2025-10-15 23:44:46'],
            ['id' => 5, 'user_id' => 1, 'video_id' => 6, 'created_at' => '2025-10-17 07:01:48', 'updated_at' => '2025-10-17 07:01:48'],
            ['id' => 6, 'user_id' => 1, 'video_id' => 5, 'created_at' => '2025-10-18 07:21:49', 'updated_at' => '2025-10-18 07:21:49'],
            ['id' => 7, 'user_id' => 1, 'video_id' => 4, 'created_at' => '2025-10-19 00:22:25', 'updated_at' => '2025-10-19 00:22:25'],
            ['id' => 9, 'user_id' => 2, 'video_id' => 2, 'created_at' => '2025-10-19 00:44:50', 'updated_at' => '2025-10-19 00:44:50'],
        ]);

        // Seed Comments
        DB::table('comments')->insert([
            ['id' => 26, 'user_id' => 1, 'video_id' => 4, 'parent_id' => null, 'content' => 'Ghê đấy', 'created_at' => '2025-10-19 00:22:39', 'updated_at' => '2025-10-19 00:22:39'],
            ['id' => 27, 'user_id' => 1, 'video_id' => 7, 'parent_id' => null, 'content' => 'Ghê', 'created_at' => '2025-10-19 01:30:58', 'updated_at' => '2025-10-19 01:30:58'],
        ]);

        // Seed Favorites
        DB::table('favorites')->insert([
            ['id' => 3, 'user_id' => 1, 'video_id' => 7, 'created_at' => '2025-10-18 06:43:18', 'updated_at' => '2025-10-18 06:43:18'],
            ['id' => 4, 'user_id' => 1, 'video_id' => 6, 'created_at' => '2025-10-18 06:51:13', 'updated_at' => '2025-10-18 06:51:13'],
            ['id' => 5, 'user_id' => 1, 'video_id' => 5, 'created_at' => '2025-10-18 07:21:50', 'updated_at' => '2025-10-18 07:21:50'],
            ['id' => 6, 'user_id' => 1, 'video_id' => 4, 'created_at' => '2025-10-19 00:22:26', 'updated_at' => '2025-10-19 00:22:26'],
            ['id' => 7, 'user_id' => 2, 'video_id' => 2, 'created_at' => '2025-10-19 00:48:38', 'updated_at' => '2025-10-19 00:48:38'],
        ]);

        // Seed Messages
        DB::table('messages')->insert([
            ['id' => 1, 'sender_id' => 1, 'receiver_id' => 2, 'message' => 'Hello', 'is_read' => 1, 'created_at' => '2025-10-17 19:09:06', 'updated_at' => '2025-10-17 19:09:08'],
            ['id' => 2, 'sender_id' => 1, 'receiver_id' => 2, 'message' => 'Bạn bao nhiêu tuổi', 'is_read' => 1, 'created_at' => '2025-10-17 19:10:55', 'updated_at' => '2025-10-17 19:10:57'],
            ['id' => 3, 'sender_id' => 1, 'receiver_id' => 2, 'message' => 'Hello', 'is_read' => 1, 'created_at' => '2025-10-17 19:15:32', 'updated_at' => '2025-10-17 19:15:58'],
            ['id' => 4, 'sender_id' => 1, 'receiver_id' => 2, 'message' => 'Hello', 'is_read' => 1, 'created_at' => '2025-10-18 02:18:24', 'updated_at' => '2025-10-18 02:18:28'],
            ['id' => 5, 'sender_id' => 1, 'receiver_id' => 2, 'message' => 'Bro', 'is_read' => 1, 'created_at' => '2025-10-18 02:21:01', 'updated_at' => '2025-10-18 02:21:03'],
            ['id' => 6, 'sender_id' => 2, 'receiver_id' => 1, 'message' => '?', 'is_read' => 1, 'created_at' => '2025-10-18 02:22:26', 'updated_at' => '2025-10-18 02:22:30'],
            ['id' => 7, 'sender_id' => 2, 'receiver_id' => 1, 'message' => 'Tôi tên là Khang ?', 'is_read' => 1, 'created_at' => '2025-10-18 02:24:02', 'updated_at' => '2025-10-18 02:24:04'],
            ['id' => 8, 'sender_id' => 2, 'receiver_id' => 1, 'message' => 'Hello', 'is_read' => 1, 'created_at' => '2025-10-18 02:24:47', 'updated_at' => '2025-10-19 00:15:04'],
            ['id' => 9, 'sender_id' => 1, 'receiver_id' => 2, 'message' => '?', 'is_read' => 0, 'created_at' => '2025-10-19 00:15:17', 'updated_at' => '2025-10-19 00:15:17'],
        ]);

        // Seed Reports
        DB::table('reports')->insert([
            ['id' => 1, 'reporter_id' => 1, 'reported_user_id' => 2, 'reason' => 'spam', 'description' => 'Như d', 'status' => 'pending', 'created_at' => '2025-10-19 00:21:51', 'updated_at' => '2025-10-19 00:21:51'],
        ]);

        // Seed Notifications
        DB::table('notifications')->insert([
            ['id' => 5, 'user_id' => 3, 'actor_id' => 1, 'type' => 'comment', 'notifiable_id' => 7, 'notifiable_type' => 'App\\Models\\Video', 'data' => '{"comment":"Ghê"}', 'read_at' => null, 'created_at' => '2025-10-19 01:30:58', 'updated_at' => '2025-10-19 01:30:58'],
        ]);

        // Seed Video Reports
        DB::table('video_reports')->insert([
            ['id' => 1, 'user_id' => 1, 'video_id' => 7, 'reason' => 'spam', 'description' => 'Good', 'status' => 'pending', 'created_at' => '2025-10-19 01:39:58', 'updated_at' => '2025-10-19 01:39:58'],
        ]);

        // Seed Not Interested
        DB::table('not_interested')->insert([
            ['id' => 1, 'user_id' => 1, 'video_id' => 6, 'created_at' => '2025-10-19 01:40:11', 'updated_at' => '2025-10-19 01:40:11'],
        ]);

        // Seed Live Streams
        DB::table('live_streams')->insert([
            ['id' => 1, 'user_id' => 1, 'title' => 'Coder', 'description' => 'Dạy code', 'thumbnail' => null, 'status' => 'ended', 'viewers_count' => 15, 'started_at' => '2025-10-19 09:19:57', 'ended_at' => '2025-10-19 12:09:08', 'created_at' => '2025-10-19 09:19:57', 'updated_at' => '2025-10-19 12:09:08'],
            ['id' => 2, 'user_id' => 1, 'title' => 'Coder', 'description' => 'Dạy code', 'thumbnail' => null, 'status' => 'ended', 'viewers_count' => 4, 'started_at' => '2025-10-19 12:25:17', 'ended_at' => '2025-10-19 12:27:05', 'created_at' => '2025-10-19 12:25:17', 'updated_at' => '2025-10-19 12:27:05'],
            ['id' => 3, 'user_id' => 1, 'title' => 'Coder', 'description' => 'Anh coder', 'thumbnail' => null, 'status' => 'ended', 'viewers_count' => 5, 'started_at' => '2025-10-19 12:31:51', 'ended_at' => '2025-10-19 12:34:48', 'created_at' => '2025-10-19 12:31:51', 'updated_at' => '2025-10-19 12:34:48'],
            ['id' => 4, 'user_id' => 1, 'title' => 'Code', 'description' => 'Code', 'thumbnail' => null, 'status' => 'ended', 'viewers_count' => 4, 'started_at' => '2025-10-19 12:35:09', 'ended_at' => '2025-10-19 12:37:44', 'created_at' => '2025-10-19 12:35:09', 'updated_at' => '2025-10-19 12:37:44'],
            ['id' => 5, 'user_id' => 1, 'title' => 'Coder', 'description' => 'Coder', 'thumbnail' => null, 'status' => 'ended', 'viewers_count' => 11, 'started_at' => '2025-10-19 12:39:51', 'ended_at' => '2025-10-19 12:50:57', 'created_at' => '2025-10-19 12:39:51', 'updated_at' => '2025-10-19 13:22:25'],
        ]);

        $this->command->info('Database seeded successfully!');
    }
}
