<?php

namespace Visualbuilder\FilamentVersionable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Visualbuilder\Versionable\Versionable;
use Visualbuilder\Versionable\VersionStrategy;

class Post extends Model
{
    use Versionable;

    protected $fillable = ['title', 'content', 'status'];

    protected $versionable = ['title', 'content', 'status'];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;
}
