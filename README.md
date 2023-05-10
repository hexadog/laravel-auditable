![Package Logo](https://banners.beyondco.de/Auditable.png?theme=light&packageManager=composer+require&packageName=hexadog%2Flaravel-auditable&pattern=architect&style=style_1&description=Know+who+manipulates+your+models+in+your+Laravel+application&md=1&showWatermark=1&fontSize=100px&images=identification)

<p align="center">
    <a href="https://packagist.org/packages/hexadog/laravel-auditable">
        <img src="https://poser.pugx.org/hexadog/laravel-auditable/v" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-auditable">
        <img src="https://poser.pugx.org/hexadog/laravel-auditable/downloads" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-auditable">
        <img src="https://poser.pugx.org/hexadog/laravel-auditable/license" alt="License">
    </a>
</p>

<code>hexadog/laravel-auditable</code> helps you to automatically register the user making action on your models.

<!-- omit in toc -->
## Installation
This package requires PHP 7.3 and Laravel 7.0 or higher.

To get started, install Auditable using Composer:
```shell
composer require hexadog/laravel-auditable
```

The package will automatically register its service provider.

## Usage
Use the new `auditable` macro into your migrations.
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('posts', function (Blueprint $table) {
    $table->auditable();
});

Schema::table('posts', function (Blueprint $table) {
    $table->dropAuditable();
});
```

It will add the following columns:
- created_at
- created_by
- updated_at
- updated_by
- deleted_at
- deleted_by

**Notice:** You don't have to use `timestamps()` nor `sofDeletes()` macros. Auditable macro will integrate them for you.

_If you altered you DB and want to drop auditable columns you can use the macro `$table->dropAuditable()`;_

Once database has been migrated you can use the `Auditable` trait into your model.

```php
use Hexadog\Auditable\Models\Traits\Auditable;

class Post extends Models
{
    use Auditable;

    // ...
}
```

This way the id of the user responsible of the action (creation, update, soft deletion) is automatically registered into your model in the associated column each time the data is touched. You don't have to do anything.

## Retreive user responsible of the action
You can retreive the user respoinsible of the last action (create, update, delete) by calling one of the helper methods provided by the trait.

To determine the user responsible of the model creation you may use the `created_by` attribute to get the id or the `createdBy` relation to get the target model:
```php
// Get user responsible of the creation of the post
$post->createdBy;

// Get the creation date
$post->created_at;
```

To determine the user responsible of the last model update you may use the `updated_by` attribute to get the id or the `updatedBy` relation to get the target model:
```php
// Get user responsible of the last update of the post
$post->updatedBy;

// Get the last update date
$post->updated_at;
```

To determine the user responsible of the model deletion (only if the model uses `SoftDeletes` trait) you may use the `deleted_by` attribute to get the id or the `deletedBy` relation to get the target model:
```php
// Get user responsible of the post deletion (ONLY if soft deletes are used)
$post->deletedBy;

// Get the soft deletion date
$post->deleted_at;
```

## Log changes
To log changes you can use [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) package.

<!-- omit in toc -->
## Credits
- Logo made by [BeyondCode](https://banners.beyondco.de/)

<!-- omit in toc -->
## License
Laravel Auditable is open-sourced software licensed under the [MIT license](LICENSE).