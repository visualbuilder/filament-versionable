<?php

use Visualbuilder\FilamentVersionable\Tests\Models\Admin;
use Visualbuilder\FilamentVersionable\Tests\Models\OrganisationUser;
use Visualbuilder\FilamentVersionable\Tests\Models\Post;
use Visualbuilder\FilamentVersionable\Tests\Models\User;

uses()->group('polymorphic');

it('tracks correct user type in version model', function () {
    $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com']);
    $orgUser = OrganisationUser::create(['name' => 'Org', 'email' => 'org@example.com']);

    $this->actingAs($admin);
    $post1 = Post::create(['title' => 'Post 1', 'content' => 'Content 1']);

    $this->actingAs($orgUser);
    $post2 = Post::create(['title' => 'Post 2', 'content' => 'Content 2']);

    $version1 = $post1->lastVersion;
    $version2 = $post2->lastVersion;

    expect($version1->user)->toBeInstanceOf(Admin::class);
    expect($version1->user->name)->toBe('Admin');
    expect($version1->user_type)->toBe(Admin::class);

    expect($version2->user)->toBeInstanceOf(OrganisationUser::class);
    expect($version2->user->name)->toBe('Org');
    expect($version2->user_type)->toBe(OrganisationUser::class);
});

it('eager loads polymorphic user relationships correctly', function () {
    $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com']);
    $orgUser = OrganisationUser::create(['name' => 'Org', 'email' => 'org@example.com']);

    $this->actingAs($admin);
    $post1 = Post::create(['title' => 'Post 1', 'content' => 'Content 1']);

    $this->actingAs($orgUser);
    $post2 = Post::create(['title' => 'Post 2', 'content' => 'Content 2']);

    // Load versions with eager loading
    $versions = \Visualbuilder\Versionable\Version::with('user')->get();

    $version1 = $versions->firstWhere('versionable_id', $post1->id);
    $version2 = $versions->firstWhere('versionable_id', $post2->id);

    expect($version1->user)->toBeInstanceOf(Admin::class);
    expect($version2->user)->toBeInstanceOf(OrganisationUser::class);
});

it('can restore to previous version created by different user type', function () {
    $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com']);
    $user = User::create(['name' => 'User', 'email' => 'user@example.com']);

    // Admin creates post
    $this->actingAs($admin);
    $post = Post::create(['title' => 'Admin Version', 'content' => 'Content']);

    // User updates it
    $this->actingAs($user);
    $post->update(['title' => 'User Version']);

    // Get the first version (created by admin)
    $firstVersion = $post->versions->first();

    // Restore to admin's version
    $firstVersion->revert();

    $post->refresh();
    expect($post->title)->toBe('Admin Version');
});

it('handles soft deleted user correctly', function () {
    $user = User::create(['name' => 'Deleted User', 'email' => 'deleted@example.com']);
    $this->actingAs($user);

    $post = Post::create(['title' => 'Post Title', 'content' => 'Post Content']);

    // Soft delete the user
    $user->delete();

    // Should still be able to access the version and the soft-deleted user
    $version = $post->lastVersion;
    expect($version->user)->not->toBeNull();
    expect($version->user->name)->toBe('Deleted User');
    expect($version->user->trashed())->toBeTrue();
});

it('handles null user correctly', function () {
    // Log out to create post without authentication
    auth()->logout();

    $post = Post::create(['title' => 'No User Post', 'content' => 'No User Content']);

    $version = $post->lastVersion;
    expect($version->user_id)->toBeNull();
    expect($version->user_type)->toBeNull();
    expect($version->user)->toBeNull();
});

it('stores different user types in sequence', function () {
    $user = User::create(['name' => 'User One', 'email' => 'user@example.com']);
    $admin = Admin::create(['name' => 'Admin Two', 'email' => 'admin@example.com']);
    $orgUser = OrganisationUser::create(['name' => 'Org Three', 'email' => 'org@example.com']);

    // User creates post
    $this->actingAs($user);
    $post = Post::create(['title' => 'V1', 'content' => 'Content']);

    // Admin updates it
    $this->actingAs($admin);
    $post->update(['title' => 'V2']);

    // Org user updates it
    $this->actingAs($orgUser);
    $post->update(['title' => 'V3']);

    $versions = $post->versions()->with('user')->get();

    expect($versions)->toHaveCount(3);
    expect($versions[0]->user)->toBeInstanceOf(User::class);
    expect($versions[1]->user)->toBeInstanceOf(Admin::class);
    expect($versions[2]->user)->toBeInstanceOf(OrganisationUser::class);
});

it('correctly stores polymorphic relationship in database', function () {
    $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com']);
    $this->actingAs($admin);

    $post = Post::create(['title' => 'Test Post', 'content' => 'Content']);

    $this->assertDatabaseHas('versions', [
        'versionable_id' => $post->id,
        'versionable_type' => Post::class,
        'user_id' => $admin->id,
        'user_type' => Admin::class,
    ]);
});
