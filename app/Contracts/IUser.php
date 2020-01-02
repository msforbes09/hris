<?php

namespace App\Contracts;

interface IUser
{
    public function allPerPage($per_page);

    public function getUserbyId($id);

    public function getUserByUsername($username);

    public function getUserByEmail($email);

    public function getUserWithType($id);

    public function store($request);

    public function update($request, $id);

    public function destroy($id);
}