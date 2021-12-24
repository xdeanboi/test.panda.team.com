<?php

namespace PandaTeam\Models\Users;

use PandaTeam\Exceptions\InvalidArgumentException;
use PandaTeam\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{
    protected $nickname;
    protected $email;
    protected $role;
    protected $passwordHash;
    protected $authToken;
    protected $createdAt;

    /**
     * @param string $nickname
     */
    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $authToken
     */
    public function setAuthToken(string $authToken): void
    {
        $this->authToken = $authToken;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function refreshAuthToken(): void
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    protected static function getTableName(): string
    {
        return 'users';
    }

    public static function login(array $fieldsUser): User
    {

        if (empty($fieldsUser['nickname'])) {
            throw new InvalidArgumentException('Поле Nickname не может быть пустым');
        }

        if (empty($fieldsUser['password'])) {
            throw new InvalidArgumentException('Поле Password не может быть пустым');
        }

        $user = User::findByOneColumn('nickname', $fieldsUser['nickname']);

        if ($user === null) {
            throw new InvalidArgumentException('Такого пользователя не существует');
        }

        if (!password_verify($fieldsUser['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неверный пароль');
        }

        $user->refreshAuthToken();
        $user->save();

        return $user;
    }

    public static function loginOut(): void
    {
        setcookie('token', '', -1, '/', '');

        header('Location: /', true, 302);
        return;
    }

    public static function getAdmin(): ?self
    {
        //Admin admin / test.panda.team@gmail.com admin123456789admin
        $admin = User::findByOneColumn('role', 'admin');

        if (empty($admin)) {
            return null;
        }

        return $admin;
    }
}