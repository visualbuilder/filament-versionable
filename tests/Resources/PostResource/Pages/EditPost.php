<?php

namespace Visualbuilder\FilamentVersionable\Tests\Resources\PostResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Visualbuilder\FilamentVersionable\Tests\Resources\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
