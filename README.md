English | [中文](./README-CN.md)

<div align="center">

# LARAVEL HASIN

<p>
    <a href="https://github.com/laravel-ready/hasin/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/laravel-ready/hasin/releases" ><img src="https://img.shields.io/github/release/laravel-ready/hasin.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/laravel-ready/hasin"><img src="https://img.shields.io/packagist/dt/laravel-ready/hasin.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-8+-59a9f8.svg?style=flat" /></a> 
</p>

</div>

The `hasin` is composer package based on `where in` syntax to query the relationship of `laravel ORM`, which can replace `has` based on `where exists` syntax in some business scenarios to obtain higher performance.

# Installation
| Laravel Version | Install command |
| ---- | ---- |
| Laravel 11 | ``` composer require laravel-ready/hasin:^3.0 ``` |
| Laravel 10 | ``` composer require laravel-ready/hasin:^2.1 ``` |
| Laravel 9 | ``` composer require laravel-ready/hasin:^2.0 ``` |
| Laravel 5.5 ~ 8 | ``` composer require laravel-ready/hasin:^1.0 ``` |

# Introductions

The relationship of `laravel ORM` is very powerful, and the query `has` based on the relationship also provides us with many flexible calling methods. However, in some cases, `has` is implemented with **where exists** syntax.

For example:
```php
// User hasMany Post
User::has('posts')->get();
```
#### `select * from users where exists (select * from posts where users.id = posts.user_id)`
> 'exists' is a loop to the external table, and then queries the internal table (subQuery) every time. Because the index used for the query of the internal table (the internal table is efficient, so it can be used as a large table), and how much of the external table needs to be traversed, it is inevitable (try to use a small table), so the use of exists for the large internal table can speed up the efficiency.

However, when the **User** has a large amount of data, there will be performance problems, so the **where in** syntax will greatly improve the performance.

#### `select * from users where users.id in (select posts.user_id from posts)`
> 'in' is to hash connect the appearance and inner table, first query the inner table, then match the result of the inner table with the appearance, and use the index for the outer table (the appearance is efficient, and large tables can be used). Most of the inner tables need to be queried, which is inevitable. Therefore, using 'in' with large appearance can speed up the efficiency.

Therefore, the use of `has(hasMorph)` or `hasIn(hasMorphIn)` in code should be determined by **data size**

```php
/**
 * SQL:
 * 
 * select * from `users` 
 * where exists 
 *   ( 
 *      select * from `posts` 
 *      where `users`.`id` = `posts`.`user_id` 
 *   ) 
 * limit 10 offset 0
 */
$users = User::has('posts')->paginate(10);

/**
 * SQL:
 * 
 * select * from `users` 
 * where `users`.`id` in  
 *   ( 
 *      select `posts`.`user_id` from `posts` 
 *   ) 
 * limit 10 offset 0
 */
$users = User::hasIn('posts')->paginate(10);
```

# Usage example

`hasIn(hasMorphIn)` supports all `Relations` in `laravel ORM`. The call mode and internal implementation are completely consistent with `has(hasMorph)` of the framework.

> hasIn

```php
// hasIn
User::hasIn('posts')->get();

// orHasIn
User::where('age', '>', 18)->orHasIn('posts')->get();

// doesntHaveIn
User::doesntHaveIn('posts')->get();

// orDoesntHaveIn
User::where('age', '>', 18)->orDoesntHaveIn('posts')->get();
```

> whereHasIn

```php
// whereHasIn
User::whereHasIn('posts', function ($query) {
    $query->where('votes', '>', 10);
})->get();

// orWhereHasIn
User::where('age', '>', 18)->orWhereHasIn('posts', function ($query) {
    $query->where('votes', '>', 10);
})->get();

// whereDoesntHaveIn
User::whereDoesntHaveIn('posts', function ($query) {
    $query->where('votes', '>', 10);
})->get();

// orWhereDoesntHaveIn
User::where('age', '>', 18)->orWhereDoesntHaveIn('posts', function ($query) {
    $query->where('votes', '>', 10);
})->get();
```

> hasMorphIn

```php
Image::hasMorphIn('imageable', [Post::class, Comment::class])->get();
```

### Nested Relation

```php
User::hasIn('posts.comments')->get();
```

# Testing
```bash
composer test
```
>**Tips**: before testing, you need to configure your database connection in the `phpunit.xml.dist`.

# License
[MIT](./LICENSE)
