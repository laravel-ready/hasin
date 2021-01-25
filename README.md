English | [中文](./README-CN.md)

<div align="center">

# LARAVEL HASIN

<p>
    <a href="https://github.com/biiiiiigmonster/hasin/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/biiiiiigmonster/hasin/releases" ><img src="https://img.shields.io/github/release/biiiiiigmonster/hasin.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/biiiiiigmonster/hasin"><img src="https://img.shields.io/packagist/dt/biiiiiigmonster/hasin.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-7+-59a9f8.svg?style=flat" /></a> 
</p>

</div>

`hasin` is an extension package based on `where in` syntax to query the association relationship of `laravel ORM`, which can replace has based on `where exists` syntax in `laravel ORM` in some business scenarios to obtain higher performance.

# Environment

- PHP >= 7.1
- laravel >= 5.8


# Installation

```bash
composer require biiiiiigmonster/hasin
```

# Introductions

The relationship of `laravel ORM` is very powerful, and the query `has` based on the relationship also provides us with many flexible calling methods. However, in some cases, `has` is implemented with **where exists** syntax.

For example:
```php
// User hasMany Post
User::has('posts')->get();
```
#### `select * from users where exists (select * from posts where user.id=post.user_id)`
> 'exists' is a loop to the external table, and then queries the internal table (subquery) every time. Because the index used for the query of the internal table (the internal table is efficient, so it can be used as a large table), and how much of the external table needs to be traversed, it is inevitable (try to use a small table), so the use of exists for the large internal table can speed up the efficiency.

However, when the **users** has a large amount of data, there will be performance problems, so the **where in** syntax will greatly improve the performance.

#### `select * from users where user.id in (select posts.user_id from posts)`
> 'in' is to hash connect the appearance and inner table, first query the inner table, then match the result of the inner table with the appearance, and use the index for the outer table (the appearance is efficient, and large tables can be used). Most of the inner tables need to be queried, which is inevitable. Therefore, using 'in' with large appearance can speed up the efficiency.

Therefore, it is recommended to use `hasIn(hasMorphIn)` instead of `has(hasMorph)` in code to achieve higher performance.

```php
<?php
/**
 * SQL:
 * 
 * select * from `product` 
 * where exists 
 *   ( 
 *      select * from `product_skus` 
 *      where `product`.`id` = `product_skus`.`p_id` 
 *      and `product_skus`.`deleted_at` is null 
 *   ) 
 * and `product`.`deleted_at` is null 
 * limit 10 offset 0
 */
$products = Product::has('skus')->paginate(10);

/**
 * SQL:
 * 
 * select * from `product` 
 * where `product`.`id` IN  
 *   ( 
 *      select `product_skus`.`p_id` from `product_skus` 
 *      and `product_skus`.`deleted_at` is null 
 *   ) 
 * and `product`.`deleted_at` is null 
 * limit 10 offset 0
 */
$products = Product::hasIn('skus')->paginate(10);
```

# Usage example

You should add it to the `providers` array in the `config/app.php` file.
```php
<?php
    // ...
    
    'providers' => [
        // ...
        
        BiiiiiigMonster\Hasin\HasinServiceProvider::class,
    ],
```
`hasIn(hasMorphIn)` supports all `Relation` in `laravel ORM`. The input parameter call and internal implementation process are completely consistent with `has(hasMorph)` of the framework, and can be used or replaced safely

> hasIn

```php
// hasIn
Product::hasIn('skus')->get();

// orHasIn
Product::where('name', 'like', '%chocolates%')->orHasIn('skus')->get();

// doesntHaveIn
Product::doesntHaveIn('skus')->get();

// orDoesntHaveIn
Product::where('name', 'like', '%chocolates%')->orDoesntHaveIn('skus')->get();
```

> whereHasIn

```php
// whereHasIn
Product::whereHasIn('skus', function ($query) {
    $query->where('sales', '>', 10);
})->get();

// orWhereHasIn
Product::where('name', 'like', '%chocolates%')->orWhereHasIn('skus', function ($query) {
    $query->where('sales', '>', 10);
})->get();

// whereDoesntHaveIn
Product::whereDoesntHaveIn('skus', function ($query) {
    $query->where('sales', '>', 10);
})->get();

// orWhereDoesntHaveIn
Product::where('name', 'like', '%chocolates%')->orWhereDoesntHaveIn('skus', function ($query) {
    $query->where('sales', '>', 10);
})->get();
```

> hasMorphIn

```php
Image::hasMorphIn('imageable', [Product::class, Brand::class])->get();
```

### Nested Relation

```php
Product::hasIn('attrs.values')->get();
```

### Self Relation 
```php
Category::hasIn('children')->get();
```

# License
[MIT](./LICENSE)
