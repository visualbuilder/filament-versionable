<?php

namespace Visualbuilder\FilamentVersionable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Visualbuilder\Versionable\VersionStrategy;
use Visualbuilder\Versionable\Versionable;

class Post extends Model
{
    use Versionable;

    protected $fillable = ['title', 'content', 'status'];

    protected $versionable = ['title', 'content', 'status'];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;
}
