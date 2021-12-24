<?php

namespace PandaTeam\Controllers;

use PandaTeam\Exceptions\ForbiddenException;
use PandaTeam\Exceptions\InvalidArgumentException;
use PandaTeam\Models\Users\User;
use PandaTeam\Models\Users\UserAuthService;

class MainController extends AbstractController
{
    public function main(): void
    {
        if (!empty($this->user)) {
            throw new ForbiddenException('Вы уже авторизованы');
        }

        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);

                UserAuthService::createToken($user);

                header('Location: /users/main', true, 302);
                return;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('main/main.php', ['error' => $e->getMessage(),
                    'title' => 'Error']);
                return;
            }
        }

        $this->view->renderHtml('main/main.php', ['title' => 'Авторизация']);
    }
}