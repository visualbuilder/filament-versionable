<?php

namespace Visualbuilder\FilamentVersionable\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrganisationUser extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'password'];
}
