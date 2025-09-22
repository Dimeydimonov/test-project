<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Заполнение базы данных тестовыми данными.
     */
    public function run(): void
    {
        // Создаем администратора, если его еще нет
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Создаем тестового пользователя, если его еще нет
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Тестовый пользователь',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Создаем коллекцию пользователей, начиная с тестового пользователя
        $users = collect([$testUser]);
        
        // Создаем еще 5 случайных пользователей, если их меньше 5
        if (User::count() < 7) { // 1 admin + 1 test user + 5 random users
            $users = $users->merge(User::factory(5)->create());
        } else {
            $users = $users->merge(User::where('email', '!=', 'admin@example.com')
                ->where('email', '!=', 'user@example.com')
                ->take(5)
                ->get());
        }

        // Создаем несколько категорий
        $categories = Category::factory(5)->create();

        // Создаем произведения искусства для каждой категории
        $categories->each(function ($category) use ($admin, $users) {
            // Создаем несколько произведений для каждой категории
            $artworks = Artwork::factory(rand(3, 10))
                ->for($category)
                ->for($users->random())
                ->create();

            // Для каждого произведения создаем комментарии
            $artworks->each(function ($artwork) use ($users) {
                // Создаем корневые комментарии (не более 3 на произведение)
                $commentCount = min(3, $users->count() - 1); // Ensure we don't request more comments than users
                $comments = Comment::factory($commentCount)
                    ->for($artwork)
                    ->for($users->random())
                    ->create();

                // Для некоторых комментариев создаем ответы (не более 2 ответов на комментарий)
                $comments->take(rand(1, $comments->count()))->each(function ($comment) use ($users, $artwork) {
                    $replyCount = min(2, $users->count() - 1); // Ensure we don't request more replies than users
                    Comment::factory($replyCount)
                        ->for($artwork)
                        ->for($users->random())
                        ->create([
                            'parent_id' => $comment->id,
                        ]);
                });
            });
        });

        // Создаем несколько избранных произведений
        Artwork::inRandomOrder()->take(5)->update(['is_featured' => true]);

        // Создаем лайки для произведений
        $users = User::all();
        $artworks = Artwork::all();

        // Каждый пользователь лайкает случайные произведения
        $users->each(function ($user) use ($artworks) {
            $artworksToLike = $artworks->random(rand(5, 15));
            
            foreach ($artworksToLike as $artwork) {
                // Пропускаем, если пользователь - автор произведения
                if ($artwork->user_id === $user->id) {
                    continue;
                }
                
                // Создаем лайк с случайной датой в пределах последнего года
                $like = new \App\Models\Like([
                    'user_id' => $user->id,
                    'likeable_id' => $artwork->id,
                    'likeable_type' => get_class($artwork),
                    'created_at' => now()->subDays(rand(0, 365))->subHours(rand(0, 23))
                ]);
                
                $artwork->likes()->save($like);
            }
        });

        $this->command->info('База данных успешно заполнена тестовыми данными!');
        $this->command->info('Администратор: admin@example.com / password');
        $this->command->info('Пользователь: user@example.com / password');
        $this->command->info('Всего произведений: ' . Artwork::count());
        $this->command->info('Всего пользователей: ' . User::count());
        $this->command->info('Всего лайков: ' . \App\Models\Like::count());
    }
}
