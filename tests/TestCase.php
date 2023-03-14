<?php

namespace LaravelReady\Hasin\Tests;

use LaravelReady\Hasin\HasinServiceProvider;
use LaravelReady\Hasin\Tests\Models\Comment;
use LaravelReady\Hasin\Tests\Models\Country;
use LaravelReady\Hasin\Tests\Models\History;
use LaravelReady\Hasin\Tests\Models\Image;
use LaravelReady\Hasin\Tests\Models\Phone;
use LaravelReady\Hasin\Tests\Models\Post;
use LaravelReady\Hasin\Tests\Models\Role;
use LaravelReady\Hasin\Tests\Models\Supplier;
use LaravelReady\Hasin\Tests\Models\Tag;
use LaravelReady\Hasin\Tests\Models\User;
use LaravelReady\Hasin\Tests\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    private Migration $migration;

    protected function getPackageProviders($app)
    {
        return [
            HasinServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->migration->up();
    }

    protected function destroyDatabaseMigrations()
    {
        $this->migration->down();
    }

    protected function defineDatabaseSeeders()
    {
        $tags = Tag::factory(20)->create();
        $countries = Country::factory(15)->create();
        $suppliers = Supplier::factory(15)->create();
        $roles = Role::factory(10)->create();

        $users = User::factory(15)
            ->has(History::factory())
            ->has(Phone::factory())
            ->has(Image::factory(3))
            ->hasAttached($roles->random(5))
            ->sequence(fn () => ['country_id' => $countries->pluck('id')->random()])
            ->sequence(fn () => ['supplier_id' => $suppliers->pluck('id')->random()])
            ->create();

        $posts = Post::factory(15)
            ->sequence(fn () => ['user_id' => $users->pluck('id')->random()])
            ->hasAttached($tags->random(15))
            ->create();

        $videos = Video::factory(15)->hasAttached($tags->random(15))->create();

        $posts->random(5)->map(function ($post) {
            Comment::factory(3)->for($post, 'commentable')->create();
            Image::factory(2)->for($post, 'imageable')->create();
        });

        $videos->random(5)->map(function ($video) {
            Comment::factory(3)->for($video, 'commentable')->create();
        });
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.connections.mysql.prefix', 'hasin_test_');

        Schema::defaultStringLength(191);
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'LaravelReady\\Hasin\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->migration = include __DIR__ . '/../database/migrations/create_hasin_test_table.php';
    }
}
