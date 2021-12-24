<?php

namespace PandaTeam\Controllers;

use PandaTeam\Models\Users\UserAuthService;
use PandaTeam\View\View;

abstract class AbstractController
{
    protected $view;
    protected $user;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
        $this->user = UserAuthService::getUserByToken();
        $this->view->setVars('user', $this->user);
    }
}