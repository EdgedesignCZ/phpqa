<?php

namespace Edge\QA;

function pathToBinary($tool)
{
    return escapePath(COMPOSER_BINARY_DIR . $tool);
}

function escapePath($path)
{
    return "\"{$path}\"";
}

function commonPath(array $dirsOrFiles)
{
    if (!$dirsOrFiles) {
        return;
    }
    $isNotWindows = strtoupper(substr(PHP_OS, 0, 3)) != 'WIN';

    $dirs = [];
    $dirsCount = [];
    foreach (array_values($dirsOrFiles) as $i => $fileOrDir) {
        $path = is_dir($fileOrDir) ? $fileOrDir : dirname($fileOrDir);
        $dirs[$i] = explode(DIRECTORY_SEPARATOR, $path);
        if ($isNotWindows) {
            unset($dirs[$i][0]);
        }
        $dirsCount[] = count($dirs[$i]);
    }

    $minDirsCount = min($dirsCount);
    for ($i = 0; $i < count($dirs); $i++) {
        $firstSlash = $isNotWindows ? DIRECTORY_SEPARATOR : '';
        $dirs[$i] = $firstSlash . implode(DIRECTORY_SEPARATOR, array_slice($dirs[$i], 0, $minDirsCount));
    }

    $commonDirs = array_unique($dirs);
    while (count($commonDirs) !== 1) {
        $commonDirs = array_unique(array_map('dirname', $commonDirs));
    }
    return reset($commonDirs);
}
