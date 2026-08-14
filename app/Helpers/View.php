<?php

class View {
    public static function render(string $viewPath, array $data = [], string $layout = 'app'): void {
        extract($data);
        
        $viewFile = __DIR__ . "/../../resources/views/{$viewPath}.php";
        $layoutFile = __DIR__ . "/../../resources/views/layouts/{$layout}.php";

        if (!file_exists($viewFile)) {
            die("View file not found: {$viewPath}");
        }

        // Render view content into buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === 'none') {
            echo $content;
            return;
        }

        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
