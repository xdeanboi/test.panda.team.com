<?php

namespace PandaTeam\Controllers;

use PandaTeam\Exceptions\ForbiddenException;
use PandaTeam\Exceptions\InvalidArgumentException;
use PandaTeam\Exceptions\NotFoundException;
use PandaTeam\Models\Tasks\Task;
use PandaTeam\Models\Users\User;
use PandaTeam\Services\EmailSender;

class TaskController extends AbstractController
{
    public function done(int $taskId): void
    {
        if (empty($this->user)) {
            throw new ForbiddenException('Авторизуйтесь');
        }

        $task = Task::getById($taskId);

        if (empty($task)) {
            throw new NotFoundException('Задача не найдена');
        }

        $admin = User::getAdmin();

        if (empty($admin)) {
            throw new NotFoundException('Админа не найдено');
        }

        $task->done();

        if ($task->getDone()) {
            if (!empty($admin)) {
                EmailSender::send(
                    $admin,
                    'Задача ' . $task->getName() . ' сделана',
                    'taskDone.php',
                    ['taskName' => $task->getName(),
                        'nickname' => $this->user->getNickname()]);

            } else {
                throw new NotFoundException('Админа для проверки не найдено');
            }
        }

        header('Location: /users/main', true, 302);
        return;
    }

    public function delete(int $taskId): void
    {
        if (empty($this->user)) {
            throw new ForbiddenException('Авторизуйтесь');
        }

        $task = Task::getById($taskId);

        if (empty($task)) {
            throw new NotFoundException('Задача не найдена');
        }

        if (!$task->getDone()) {
            throw new ForbiddenException('Задача ещё не сделана.');
        }

        $task->delete();


        header('Location: /users/main', 302, true);
        return;

    }

    public function add(): void
    {
        if (empty($this->user)) {
            throw new ForbiddenException('Авторизуйтесь');
        }

        $tasks = Task::findAll();

        if (empty($tasks)) {
            throw new InvalidArgumentException('На данный момент нет задач');
        }

        if (!empty($_POST)) {
            try {
                Task::add($_POST);

                header('Location: /users/main', true, 302);
                return;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/main.php', ['error' => $e->getMessage(),
                    'tasks' => $tasks,
                    'title' => 'Error']);
                return;
            }
        }

    }
}