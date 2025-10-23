<?php

namespace Visualbuilder\FilamentVersionable\Tests\Resources\PostResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Visualbuilder\FilamentVersionable\Tests\Resources\PostResource;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;
}
