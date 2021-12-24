<?php

namespace PandaTeam\Controllers;

use PandaTeam\Exceptions\ForbiddenException;
use PandaTeam\Exceptions\InvalidArgumentException;
use PandaTeam\Models\Tasks\Task;
use PandaTeam\Models\Users\User;

class UserController extends AbstractController
{
    public function main(): void
    {
        if (empty($this->user)) {
            throw new ForbiddenException('<a href="/" style="text-decoration: none; color: red">Авторизуйтесь</a>');
        }

        $tasks = Task::findAll();

        if (empty($tasks)) {
            throw new InvalidArgumentException('На данный момент нет задач');
        }

        $this->view->renderHtml('users/main.php', ['tasks' => $tasks, 'title' => 'User Main']);
    }

    public function loginOut(): void
    {
        if (empty($this->user)) {
            throw new ForbiddenException('Вы не авторизованы');
        }

        if (!empty($_COOKIE)) {
            User::loginOut();
        }
    }
}