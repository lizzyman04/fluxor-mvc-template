<?php

namespace Core;

use Exception;
use ReflectionMethod;
use ReflectionException;

class Router
{
    public static function run(array $routes): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = self::extractUrl();
        $route_found = false;

        foreach ($routes as $route => $routeConfig) {
            [$allowedMethods, $target] = self::normalizeRouteConfig($routeConfig);

            if (!in_array($requestMethod, $allowedMethods)) {
                continue;
            }

            $route_pattern = self::buildRoutePattern($route);

            if (preg_match($route_pattern, $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($route_found) {
                    throw new Exception("Ambiguous route match for URL: {$url}");
                }

                [$controller, $action] = self::parseControllerAction($target);
                self::validateController($controller, $action);

                $controllerInstance = new $controller();
                $args = self::resolveMethodParameters($controllerInstance, $action, $params);

                call_user_func_array([$controllerInstance, $action], $args);
                $route_found = true;
                break;
            }
        }

        if (!$route_found) {
            self::handleNotFound();
        }
    }

    private static function extractUrl(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $url = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $url = rtrim($url, '/');
        return $url === '' ? '/' : $url;
    }

    private static function normalizeRouteConfig($routeConfig): array
    {
        if (is_string($routeConfig)) {
            return [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $routeConfig];
        }

        $allowedMethods = $routeConfig['methods'] ?? ['GET'];
        $target = $routeConfig['controller'] ?? '';

        $allowedMethods = is_array($allowedMethods) ?
            array_map('strtoupper', $allowedMethods) :
            [strtoupper($allowedMethods)];

        return [$allowedMethods, $target];
    }

    private static function buildRoutePattern(string $route): string
    {
        $pattern = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    private static function parseControllerAction(string $target): array
    {
        if (!str_contains($target, '@')) {
            throw new Exception("Invalid route target: {$target}. Use: Controller@action");
        }

        [$controller, $action] = explode('@', $target, 2);

        if (!str_contains($controller, '\\')) {
            $controller = 'Source\\Controllers\\' . $controller;
        }

        return [$controller, $action];
    }

    private static function validateController(string $controller, string $action): void
    {
        if (!class_exists($controller)) {
            throw new Exception("Controller {$controller} not found");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            throw new Exception("Method {$action} not found in {$controller}");
        }
    }

    private static function resolveMethodParameters(object $controllerInstance, string $action, array $params): array
    {
        try {
            $methodString = $controllerInstance::class . '::' . $action;
            $reflection = ReflectionMethod::createFromMethodName($methodString);
        } catch (ReflectionException $e) {
            throw new Exception("Error reflecting method '{$action}': " . $e->getMessage());
        }

        $args = [];
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = null;
                continue;
            }

            $args[] = $params[$name] ?? null;
        }

        return $args;
    }

    private static function handleNotFound(): void
    {
        http_response_code(404);

        $controllerClass = defined('ROUTER_NOT_FOUND_CONTROLLER') ? ROUTER_NOT_FOUND_CONTROLLER : null;
        $method = defined('ROUTER_NOT_FOUND_METHOD') ? ROUTER_NOT_FOUND_METHOD : null;

        if ($controllerClass && $method && class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            (new $controllerClass())->$method();
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Not Found',
            'message' => 'The requested resource was not found',
            'path' => $_SERVER['REQUEST_URI'] ?? '/'
        ]);
    }
}