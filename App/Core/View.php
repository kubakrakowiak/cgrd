<?php
namespace App\Core;

class View
{
    public static function render(string $tpl, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require __DIR__ . '/../../views/' . $tpl . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../views/layout.php';
    }
}
