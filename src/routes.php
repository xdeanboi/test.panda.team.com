<?php

return [
    '~^$~' => [\PandaTeam\Controllers\MainController::class, 'main'],
    '~^users/main$~' => [\PandaTeam\Controllers\UserController::class, 'main'],
    '~^users/exit$~' => [\PandaTeam\Controllers\UserController::class, 'loginOut'],
    '~^task/done/(\d+)$~' => [\PandaTeam\Controllers\TaskController::class, 'done'],
    '~^task/delete/(\d+)$~' => [\PandaTeam\Controllers\TaskController::class, 'delete'],
    '~^task/add$~' => [\PandaTeam\Controllers\TaskController::class, 'add'],
];
