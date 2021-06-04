<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\Traits\DbInstanceTrait;
use PDO;

class UserManager
{
    use DbInstanceTrait;

    public function getAllUsers(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users');
        $req->execute();
        $req->setFetchMode(PDO::FETCH_CLASS, 'User');
        return $req->fetchAll();
    }

    public function getUserById(int $id)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE id=:id');
        $req->bindValue(':id', $id);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_CLASS, 'User');
        return $req->fetch();
    }

    public function createUser(array $data): void
    {
        $req = $this->dbInstance->prepare(
            'INSERT INTO users(username, password, email, role) 
                   VALUES (:username, :password, :email, :role)'
        );
        $req->bindValue(':username', $data['username']);
        $req->bindValue(':password', $data['password']);
        $req->bindValue(':email', $data['email']);
        $req->bindValue(':role', $data['role']);
        $req->execute();
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function deleteUser(int $id): void
    {
        if ($this->getUserById($id)) {
            $req = $this->dbInstance->prepare('DELETE FROM users WHERE id=:id');
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function updateUser(int $id, array $data): void
    {
        if ($this->getUserById($id)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET username=:username, email=:email, password=:password, role=:role
                     WHERE id=:id'
            );
            $req->bindValue(':username', $data['username']);
            $req->bindValue(':password', $data['password']);
            $req->bindValue(':email', $data['email']);
            $req->bindValue(':role', $data['role']);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }
}
