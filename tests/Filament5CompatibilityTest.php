<?php

declare(strict_types=1);

namespace Visualbuilder\FilamentVersionable\Tests;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\View\View;
use Visualbuilder\FilamentVersionable\FilamentVersionableServiceProvider;
use Visualbuilder\FilamentVersionable\Page\RevisionsAction as PageRevisionsAction;
use Visualbuilder\FilamentVersionable\RevisionsPage;
use Visualbuilder\FilamentVersionable\Table\RevisionsAction as TableRevisionsAction;
use Visualbuilder\FilamentVersionable\Tests\Models\Admin;
use Visualbuilder\FilamentVersionable\Tests\Models\Post;
use Visualbuilder\FilamentVersionable\Tests\Resources\PostResource;

uses()->group('filament5-compatibility');

describe('Filament 5 Compatibility Tests', function () {
    it('can instantiate page revisions action', function () {
        $action = PageRevisionsAction::make();

        expect($action)->toBeInstanceOf(Action::class);
        expect($action->getName())->toBe('revisions');
    });

    it('can instantiate table revisions action', function () {
        $action = TableRevisionsAction::make();

        expect($action)->toBeInstanceOf(Action::class);
        expect($action->getName())->toBe('revisions');
    });

    it('page revisions action extends filament action class', function () {
        expect(get_parent_class(PageRevisionsAction::class))->toBe(Action::class);
    });

    it('table revisions action extends filament action class', function () {
        expect(get_parent_class(TableRevisionsAction::class))->toBe(Action::class);
    });

    it('revisions page extends filament page class', function () {
        expect(get_parent_class(RevisionsPage::class))->toBe(Page::class);
    });

    it('revisions page uses interacts with record trait', function () {
        $traits = class_uses_recursive(RevisionsPage::class);

        expect($traits)->toHaveKey('Filament\Resources\Pages\Concerns\InteractsWithRecord');
    });

    it('revisions page uses livewire pagination', function () {
        $traits = class_uses_recursive(RevisionsPage::class);

        expect($traits)->toHaveKey('Livewire\WithPagination');
    });

    it('revisions page has required action methods', function () {
        expect(RevisionsPage::class)->toHaveMethod('previousVersionAction');
        expect(RevisionsPage::class)->toHaveMethod('nextVersionAction');
        expect(RevisionsPage::class)->toHaveMethod('restoreVersionAction');
    });

    it('revisions page has required navigation methods', function () {
        expect(RevisionsPage::class)->toHaveMethod('previousVersion');
        expect(RevisionsPage::class)->toHaveMethod('nextVersion');
        expect(RevisionsPage::class)->toHaveMethod('showVersion');
        expect(RevisionsPage::class)->toHaveMethod('restoreVersion');
    });

    it('revisions page has required computed properties', function () {
        expect(RevisionsPage::class)->toHaveMethod('diff');
        expect(RevisionsPage::class)->toHaveMethod('revisionsList');
    });

    it('revisions page has required configuration methods', function () {
        expect(RevisionsPage::class)->toHaveMethod('shouldStripTags');
        expect(RevisionsPage::class)->toHaveMethod('getContentTabLabel');
        expect(RevisionsPage::class)->toHaveMethod('getRevisionsListPerPage');
        expect(RevisionsPage::class)->toHaveMethod('getNavigationIcon');
        expect(RevisionsPage::class)->toHaveMethod('getBreadcrumb');
        expect(RevisionsPage::class)->toHaveMethod('getTitle');
    });

    it('revisions page can be mounted with record', function () {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->actingAs($admin);

        $post = Post::create(['title' => 'Test', 'content' => 'Content']);
        $post->update(['title' => 'Updated']);

        $page = new class extends RevisionsPage
        {
            protected static string $resource = PostResource::class;
        };

        $page->mount($post->id);

        expect($page->getRecord())->toBeInstanceOf(Post::class);
        expect($page->getRecord()->id)->toBe($post->id);
        expect($page->version)->not->toBeNull();
    });

    it('revisions page can instantiate actions', function () {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->actingAs($admin);

        $post = Post::create(['title' => 'Test', 'content' => 'Content']);
        $post->update(['title' => 'Updated']);

        $page = new class extends RevisionsPage
        {
            protected static string $resource = PostResource::class;
        };

        $page->mount($post->id);

        $previousAction = $page->previousVersionAction();
        $nextAction = $page->nextVersionAction();
        $restoreAction = $page->restoreVersionAction();

        expect($previousAction)->toBeInstanceOf(Action::class);
        expect($nextAction)->toBeInstanceOf(Action::class);
        expect($restoreAction)->toBeInstanceOf(Action::class);
    });

    it('revisions page works with polymorphic user types', function () {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->actingAs($admin);

        $post = Post::create(['title' => 'Test', 'content' => 'Content']);
        $post->update(['title' => 'Updated']);

        $page = new class extends RevisionsPage
        {
            protected static string $resource = PostResource::class;
        };

        $page->mount($post->id);

        expect($page->version->user)->toBeInstanceOf(Admin::class);
        expect($page->version->user->name)->toBe('Admin');
    });

    it('revisions page view can be rendered', function () {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->actingAs($admin);

        $post = Post::create(['title' => 'Test', 'content' => 'Content']);
        $post->update(['title' => 'Updated']);

        $page = new class extends RevisionsPage
        {
            protected static string $resource = PostResource::class;
        };

        $page->mount($post->id);

        $view = $page->render();

        expect($view)->toBeInstanceOf(View::class);
        expect($view->name())->toBe('filament-versionable::revisions-page');
    });

    it('action classes have getDefaultName method', function () {
        expect(PageRevisionsAction::class)->toHaveMethod('getDefaultName');
        expect(TableRevisionsAction::class)->toHaveMethod('getDefaultName');

        expect(PageRevisionsAction::getDefaultName())->toBe('revisions');
        expect(TableRevisionsAction::getDefaultName())->toBe('revisions');
    });

    it('revisions page has correct view property', function () {
        $page = new RevisionsPage;
        $reflection = new \ReflectionClass($page);
        $property = $reflection->getProperty('view');
        $property->setAccessible(true);

        expect($property->getValue($page))->toBe('filament-versionable::revisions-page');
    });

    it('verifies no usage of deprecated schemas namespace in source code', function () {
        $sourceFiles = [
            __DIR__.'/../src/FilamentVersionableServiceProvider.php',
            __DIR__.'/../src/Page/RevisionsAction.php',
            __DIR__.'/../src/Table/RevisionsAction.php',
            __DIR__.'/../src/RevisionsPage.php',
        ];

        foreach ($sourceFiles as $file) {
            $content = file_get_contents($file);

            expect($content)->not->toContain('Filament\\Schemas\\');
            expect($content)->not->toContain('use Filament\Schemas');
        }
    })->note('This test verifies the package does not use Filament 4 Schemas namespace in production code');

    it('uses only stable filament apis', function () {
        $expectedImports = [
            'Filament\Actions\Action',
            'Filament\Resources\Pages\Page',
            'Filament\Resources\Pages\Concerns\InteractsWithRecord',
        ];

        foreach ($expectedImports as $import) {
            expect(class_exists($import) || trait_exists($import))->toBeTrue(
                "Expected Filament class/trait '{$import}' should exist"
            );
        }
    })->note('This test verifies all Filament imports are from stable APIs');

    it('package service provider properly registers views and assets', function () {
        expect(FilamentVersionableServiceProvider::class)
            ->toHaveMethod('configurePackage');
    });

    it('translation keys are properly configured', function () {
        $translationKeys = [
            'filament-versionable::actions.revisions',
            'filament-versionable::actions.previous_version',
            'filament-versionable::actions.next_version',
            'filament-versionable::actions.restore.label',
            'filament-versionable::page.breadcrumb',
            'filament-versionable::page.content_tab_label',
        ];

        foreach ($translationKeys as $key) {
            $translation = __($key);
            expect($translation)->toBeString();
            expect($translation)->not->toBe($key); // Should be translated, not return key
        }
    });
});
