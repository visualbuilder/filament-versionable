<?php

namespace Visualbuilder\FilamentVersionable\Tests\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Visualbuilder\FilamentVersionable\Tests\Resources\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
