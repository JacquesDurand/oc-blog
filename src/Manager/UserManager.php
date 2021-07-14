<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\Model\User;
use App\Security\SecurityService;
use App\Traits\DbInstanceTrait;
use PDO;
use PDOException;

class UserManager
{
    use DbInstanceTrait;

    /** @var SecurityService */
    private SecurityService $securityService;

    public function __construct()
    {
        $this->connect();
        $this->securityService = new SecurityService();
    }

    public function getAllUsers(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users');
        $req->execute();
        return $this->hydrateUsers($req->fetchAll());
    }

    public function getUserById(int $id)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE id=:id');
        $req->bindValue(':id', $id);
        $req->execute();
        return $this->hydrateUser($req->fetch());
    }

    public function getUserByUsername(string $username): ?User
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE username=:username');
        $req->bindValue(':username', $username);
        $req->execute();
        if ($req->fetch()) {
            $req->execute();
            return $this->hydrateUser($req->fetch());
        } else {
            return null;
        }
    }

    public function getUserByEmail(string $email)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE email=:email');
        $req->bindValue(':email', $email);
        $req->execute();
        return $this->hydrateUser($req->fetch());
    }

    /**
     * @param array $data
     * @throws PDOException
     */
    public function createUser(array $data): void
    {
        $hashedPassword = $this->securityService->hashPassword($data['password']);

        $req = $this->dbInstance->prepare(
            'INSERT INTO users(username, password, email, role) 
                   VALUES (:username, :password, :email, :role)'
        );
        $req->bindValue(':username', $data['username']);
        $req->bindValue(':password', $hashedPassword);
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
        $hashedPassword = $this->securityService->hashPassword($data['password']);

        if ($this->getUserById($id)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET username=:username, email=:email, password=:password, role=:role
                     WHERE id=:id'
            );
            $req->bindValue(':username', $data['username']);
            $req->bindValue(':password', $hashedPassword);
            $req->bindValue(':email', $data['email']);
            $req->bindValue(':role', $data['role']);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @throws ResourceNotFoundException
     */
    public function updateUserWithoutPassword(int $id, array $data): void
    {
        if ($this->getUserById($id)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET username=:username, email=:email, role=:role
                     WHERE id=:id'
            );
            $req->bindValue(':username', $data['username']);
            $req->bindValue(':email', $data['email']);
            $req->bindValue(':role', $data['role']);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @param int $id
     * @param string $password
     * @throws ResourceNotFoundException
     */
    public function updatePassword(int $id, string $password): void
    {
        $hashedPassword = $this->securityService->hashPassword($password);

        if ($this->getUserById($id)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET password = :password
                     WHERE id=:id'
            );
            $req->bindValue(':password', $hashedPassword);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    private function hydrateUsers(array $dbUsers): array
    {
        $users = [];
        foreach ($dbUsers as $dbUser) {
            $users[] = $this->hydrateUser($dbUser);
        }
        return $users;
    }

    private function hydrateUser(array $data): User
    {
        $user = new User();
        $user->setId((int)$data['id']);
        $user->setUserName($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setRole((int)$data['role']);
        $user->setCreatedAt(new \DateTime($data['created_at']));
        $user->setUpdatedAt(new \DateTime($data['updated_at']));
        $user->setLastActivity(new \DateTime($data['last_activity']));

        return $user;
    }
}
