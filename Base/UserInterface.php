<?php namespace codesaur\Base;

interface UserInterface
{
    public function hasRole(string $roleName);
    public function hasPrivilege(string $permissionName, $roleName = null);
}
