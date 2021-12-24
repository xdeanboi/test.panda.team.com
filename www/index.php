<?php

try {
    spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/../src/' . $className . '.php';
    });

    $routes = include __DIR__ . '/../src/routes.php';
    $route = $_GET['route'] ?? '';

    $isRouteFound = false;

    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRouteFound = true;
            break;
        }
    }

    unset($matches[0]);

    if (!$isRouteFound) {
        throw new \PandaTeam\Exceptions\NotFoundException('Роут не найден');
    }

    $controllerName = $controllerAndAction[0];
    $controllerAction = $controllerAndAction[1];

    $controller = new $controllerName();
    $controller->$controllerAction(...$matches);
} catch (\PandaTeam\Exceptions\NotFoundException $e ) {
    $view = new \PandaTeam\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('404.php', ['error' => $e->getMessage()], 404);
} catch (\PandaTeam\Exceptions\DbException $e) {
    $view = new \PandaTeam\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('500.php', ['error' => $e->getMessage()], 500);
} catch (\PandaTeam\Exceptions\ForbiddenException $e) {
    $view = new \PandaTeam\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('403.php', ['error' => $e->getMessage()], 403);
}
