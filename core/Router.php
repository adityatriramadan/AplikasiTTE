<?php

class Router {
    private array $routes = [];

    public function add(string $pattern, callable $handler): void {
        $this->routes[$pattern] = $handler;
    }

    public function dispatch(string $path): bool {
        foreach ($this->routes as $pattern => $handler) {
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $handler(...$matches);
                return true;
            }
        }

        return false;
    }
}