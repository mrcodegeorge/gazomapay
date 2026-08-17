<?php

class View {
    public static function render(string $viewPath, array $data = [], string $layout = 'app'): void {
        extract($data);
        
        $viewFile = __DIR__ . "/../../resources/views/{$viewPath}.php";

        // Normalize layout path if prefix 'layouts/' was passed
        $layoutName = preg_replace('/^layouts\//', '', $layout);
        $layoutFile = __DIR__ . "/../../resources/views/layouts/{$layoutName}.php";

        if (!file_exists($viewFile)) {
            die("View file not found: {$viewPath}");
        }

        // Render view content into buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === 'none' || $layoutName === 'none') {
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
