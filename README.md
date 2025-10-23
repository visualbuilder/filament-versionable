[![Latest Version on Packagist](https://img.shields.io/packagist/v/visualbuilder/filament-versionable.svg?style=flat-square)](https://packagist.org/packages/visualbuilder/filament-versionable)
[![run-tests](https://github.com/visualbuilder/filament-versionable/actions/workflows/run-tests.yml/badge.svg?branch=4.x)](https://github.com/visualbuilder/filament-versionable/actions/workflows/run-tests.yml)

# Filament Versionable (Polymorphic User Fork)

**Fork of [mansoor/filament-versionable](https://github.com/mansoorkhan96/filament-versionable) with polymorphic user support**

This fork uses [visualbuilder/versionable](https://github.com/visualbuilder/versionable) which supports polymorphic user relationships, enabling version tracking across multiple user model types (User, Admin, Associate, EndUser, OrganisationUser, etc.).

Effortlessly manage your Eloquent model revisions in Filament. It includes:

- A Filament page to show the Diff of what has changed and who changed it
- A list of Revisions by different users
- A Restore action to restore the model to any state

![](./resources/screenshot.png)

## Installation

You can install the package via composer:

```bash
composer require visualbuilder/filament-versionable
```

Then, publish the config file and migrations:

```bash
php artisan vendor:publish --provider="Visualbuilder\Versionable\ServiceProvider"
```

Run the migration command:

```bash
php artisan migrate
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views and css to your theme css file.

```css
@import '../../../../vendor/visualbuilder/filament-versionable/resources/css/plugin.css';
@source '../../../../vendor/visualbuilder/filament-versionable/resources/**/*.blade.php';
```

## Usage

Add `Visualbuilder\Versionable\Versionable` trait to your model and set `$versionable` attributes.

**NOTE: Make sure to add `protected $versionStrategy = VersionStrategy::SNAPSHOT;` This would save all the $versionable attributes when any of them changed. There are different bug reports on using VersionStrategy::DIFF**

```php
use Visualbuilder\Versionable\VersionStrategy;

class Post extends Model
{
    use Visualbuilder\Versionable\Versionable;

    protected $versionable = ['title', 'content'];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;
}
```

Create a Revisons Resource page to show Revisions, it should extend the `Visualbuilder\FilamentVersionable\RevisionsPage`. If you were to create a Revisions page for `ArticleResource`, it would look like:

```php
namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Visualbuilder\FilamentVersionable\RevisionsPage;

class ArticleRevisions extends RevisionsPage
{
    protected static string $resource = ArticleResource::class;
}
```

Next, Add the ArticleRevisions page (that you just created) to your Resource

```php
use App\Filament\Resources\ArticleResource\Pages;

public static function getPages(): array
{
    return [
        ...
        'revisions' => Pages\ArticleRevisions::route('/{record}/revisions'),
    ];
}
```

Add `RevisionsAction` to your edit/view pages, this action would only appear when there are any versions for the model you are viewing/editing.

```php
use Visualbuilder\FilamentVersionable\Page\RevisionsAction;

protected function getHeaderActions(): array
{
    return [
        RevisionsAction::make(),
    ];
}
```

You can also add the `RevisionsAction` to your table.

```php
use Visualbuilder\FilamentVersionable\Table\RevisionsAction;

$table->actions([
    RevisionsAction::make(),
]);
```

You are all set! Your app should store the model states and you can manage them in Filament.

## Customisation

If you want to change the UI for Revisions page, you may publish the publish the views to do so.

```bash
php artisan vendor:publish --tag="filament-versionable-views"
```

If you want more control over how the versions are stored, you may read the [Visualbuilder Versionable Docs](https://github.com/visualbuilder/versionable) (based on overtrue/laravel-versionable).

## Strip Tags from Diff

You can easily remove/strip HTML tags from the diff by just overriding `shouldStripTags` method inside your revisions page.

```php
class ArticleRevisions extends RevisionsPage
{
    protected static string $resource = ArticleResource::class;

    public function shouldStripTags(): bool
    {
        return true;
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mansoor Ahmed](https://github.com/mansoorkhan96)
- [安正超](https://github.com/overtrue) for [Laravel Versionable](https://github.com/overtrue/laravel-versionable)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
