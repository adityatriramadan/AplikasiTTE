<?php

abstract class Controller {

    protected function render(string $view, array $data = []): void {
        $viewFile = BASE_PATH . '/views/' . ltrim($view, '/');
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die('View tidak ditemukan: ' . htmlspecialchars($viewFile));
        }

        extract(['data' => $data], EXTR_SKIP);
        include $viewFile;
    }

    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}