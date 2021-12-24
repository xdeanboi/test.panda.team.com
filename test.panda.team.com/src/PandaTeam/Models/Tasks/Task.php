<?php

namespace PandaTeam\Models\Tasks;

use PandaTeam\Exceptions\InvalidArgumentException;
use PandaTeam\Models\ActiveRecordEntity;

class Task extends ActiveRecordEntity
{
    protected $name;
    protected $done;
    protected $createdAt;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param bool $done
     */
    public function setDone(bool $done): void
    {
        $this->done = $done;
    }

    /**
     * @return bool
     */
    public function getDone(): bool
    {
        return $this->done;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
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
        return 'tasks';
    }

    public function done(): void
    {
        $this->setDone(true);
        $this->save();
    }

    public static function add(array $fields): self
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Введите задание');
        }

        $task = new Task();
        $task->setName($fields['name']);
        $task->setDone(false);

        $task->save();

        return $task;
    }
}