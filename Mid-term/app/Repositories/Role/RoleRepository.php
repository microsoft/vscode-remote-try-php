<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    protected $role;

    public function __construct(Role $role)
    {
        parent::__construct($role);
    }
}
