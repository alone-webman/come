<?php

namespace AloneWebMan\Come;

class Install {
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static array $pathRelation = [
        'config' => 'config/plugin/alone/come',
    ];

    /**
     * Install
     * @return void
     */
    public static function install(): void {
        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall(): void {
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation(): void {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            copy_dir(__DIR__ . "/../$source", base_path($dest));
            echo "Create $dest";
        }
        //static::deleteUse(base_path('support/Request.php'), 'alone-come');
        //static::addUse(base_path('support/Request.php'), 'alone-come', "use \AloneWebMan\come\Req;");
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation(): void {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest";
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
            $dir = rtrim(rtrim(dirname(base_path($dest)), '/'), '\\');
            if (count(glob($dir . "/*")) === 0) {
                @rmdir($dir);
            }
        }
        //static::deleteUse(base_path('support/Request.php'), 'alone-come');
    }

    public static function addUse($path, $name, $body): bool {
        $content = @file_get_contents($path);
        if ($content !== false) {
            if (!stripos($content, "//===$name-start===")) {
                $pattern = '/class\s+(\w+)\s+extends\s+(\\\\?(?:\w+\\\\)*\w+)\s*\{[\s\S]*?\}/';
                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $classEnd = ($matches[0][1] ?? 0) + (strlen($matches[0][0] ?? 0)) - 1;
                    $newContent = substr_replace($content, "\r\n//===$name-start===\r\n\r\n$body\r\n\r\n//===$name-end===\r\n", $classEnd, 0);
                    return (@file_put_contents($path, $newContent) !== false);
                }
            }
        }
        return false;
    }

    /**
     * 删除PHP文件中指定标记之间的内容（包含标记）
     * @param string $path
     * @param string $name
     * @return bool
     */
    public static function deleteUse(string $path, string $name): bool {
        $content = @file_get_contents($path);
        if ($content === false) {
            return false;
        }
        $pattern = '/\/\/===' . $name . '-start===\s*[\s\S]*?\/\/===' . $name . '-end===\s*/m';
        $newContent = preg_replace($pattern, '', $content);
        if ($newContent === null) {
            return false;
        }
        if ($newContent === $content) {
            return false;
        }
        return (@file_put_contents($path, $newContent) !== false);
    }
}