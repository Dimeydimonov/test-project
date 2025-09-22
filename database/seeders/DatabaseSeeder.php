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
    
    public function run(): void
    {
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Тестовый пользователь',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        
        $users = collect([$testUser]);
        
        
        if (User::count() < 7) { 
            $users = $users->merge(User::factory(5)->create());
        } else {
            $users = $users->merge(User::where('email', '!=', 'admin@example.com')
                ->where('email', '!=', 'user@example.com')
                ->take(5)
                ->get());
        }

        
        $categories = Category::factory(5)->create();

        
        $categories->each(function ($category) use ($admin, $users) {
            
            $artworks = Artwork::factory(rand(3, 10))
                ->for($category)
                ->for($users->random())
                ->create();

            
            $artworks->each(function ($artwork) use ($users) {
                
                $commentCount = min(3, $users->count() - 1); 
                $comments = Comment::factory($commentCount)
                    ->for($artwork)
                    ->for($users->random())
                    ->create();

                
                $comments->take(rand(1, $comments->count()))->each(function ($comment) use ($users, $artwork) {
                    $replyCount = min(2, $users->count() - 1); 
                    Comment::factory($replyCount)
                        ->for($artwork)
                        ->for($users->random())
                        ->create([
                            'parent_id' => $comment->id,
                        ]);
                });
            });
        });

        
        Artwork::inRandomOrder()->take(5)->update(['is_featured' => true]);

        
        $users = User::all();
        $artworks = Artwork::all();

        
        $users->each(function ($user) use ($artworks) {
            $artworksToLike = $artworks->random(rand(5, 15));
            
            foreach ($artworksToLike as $artwork) {
                
                if ($artwork->user_id === $user->id) {
                    continue;
                }
                
                
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
