<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Authentication\Role;
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

    /**
     * Gets all Users
     * @return array
     * @throws \Exception
     */
    public function getAllUsers(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users');
        $req->execute();
        return $this->hydrateUsers($req->fetchAll());
    }

    /**
     * Gets all admin Users
     * @return array
     * @throws \Exception
     */
    public function getAllAdminUsers(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE role=:role');
        $req->bindValue(':role', Role::ROLE_ADMIN);
        $req->execute();
        return $this->hydrateUsers($req->fetchAll());
    }

    /**
     * Get a single User by Id
     * @param int $userId
     * @return User
     * @throws \Exception
     */
    public function getUserById(int $userId): User
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE id=:id');
        $req->bindValue(':id', $userId);
        $req->execute();
        return $this->hydrateUser($req->fetch());
    }

    /**
     * Gets a single User by username
     * @param string $username
     * @return User|null
     * @throws \Exception
     */
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

    /**
     * Gets a single User by email
     * @param string $email
     * @return User
     * @throws \Exception
     */
    public function getUserByEmail(string $email): User
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users WHERE email=:email');
        $req->bindValue(':email', $email);
        $req->execute();
        return $this->hydrateUser($req->fetch());
    }

    /**
     * Creates a new User
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
     * Deletes a User by Id
     * @throws ResourceNotFoundException
     */
    public function deleteUser(int $userId): void
    {
        if ($this->getUserById($userId)) {
            $req = $this->dbInstance->prepare('DELETE FROM users WHERE id=:id');
            $req->bindValue(':id', $userId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Update a User by Id
     * @throws ResourceNotFoundException
     */
    public function updateUser(int $userId, array $data): void
    {
        $hashedPassword = $this->securityService->hashPassword($data['password']);

        if ($this->getUserById($userId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET username=:username, email=:email, password=:password, role=:role
                     WHERE id=:id'
            );
            $req->bindValue(':username', $data['username']);
            $req->bindValue(':password', $hashedPassword);
            $req->bindValue(':email', $data['email']);
            $req->bindValue(':role', $data['role']);
            $req->bindValue(':id', $userId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Updates a User without changing his password
     * @param int $userId
     * @param array $data
     * @throws ResourceNotFoundException
     */
    public function updateUserWithoutPassword(int $userId, array $data): void
    {
        if ($this->getUserById($userId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET username=:username, email=:email, role=:role
                     WHERE id=:id'
            );
            $req->bindValue(':username', $data['username']);
            $req->bindValue(':email', $data['email']);
            $req->bindValue(':role', $data['role']);
            $req->bindValue(':id', $userId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Updates a User's password
     * @param int $userId
     * @param string $password
     * @throws ResourceNotFoundException
     */
    public function updatePassword(int $userId, string $password): void
    {
        $hashedPassword = $this->securityService->hashPassword($password);

        if ($this->getUserById($userId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET password = :password
                     WHERE id=:id'
            );
            $req->bindValue(':password', $hashedPassword);
            $req->bindValue(':id', $userId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Accepts a User account's creation
     * @param int $userId
     * @throws ResourceNotFoundException
     */
    public function verifyUser(int $userId): void
    {
        if ($this->getUserById($userId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE users SET role =:role WHERE id=:id'
            );
            $req->bindValue(':role', Role::ROLE_USER_VERIFIED);
            $req->bindValue(':id', $userId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Create Users from db resources
     * @param array $dbUsers
     * @return array
     * @throws \Exception
     */
    private function hydrateUsers(array $dbUsers): array
    {
        $users = [];
        foreach ($dbUsers as $dbUser) {
            $users[] = $this->hydrateUser($dbUser);
        }
        return $users;
    }

    /**
     * Creates a User from db resources
     * @param array $data
     * @return User
     * @throws \Exception
     */
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
