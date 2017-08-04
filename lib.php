<?php
/**
 * File System
 */
namespace fs {

    use function fs\path\parent;

    function mkdir($path, $mkparent) {
        if(!exists($path)) {
            return \mkdir($path, $mkparent);
        }

        return true;
    }

    function mkparent($path) {
        return mkdir(parent($path), true);
    }

    function rmfile($path) {
        return \unlink($path);
    }

    function rmdir($path, $rmtree=false) {
        if(!$rmtree) {
            return \rmdir($path);
        }

        foreach (array_diff(scandir($path), array('.','..')) as $file) {
            is_dir("$path/$file") ? rmdir("$path/$file") : unlink("$path/$file");
        }

        return \rmdir($path);
    }

    function rm($path, $rmtree=false) {
        if (isfile($path)) {
            return rmfile($path);
        } else if(isdir($path)) {
            return rmdir($path, $rmtree);
        } else {
            return false;
        }
    }

    function symlink($linkpath, $target) {
        return \symlink($target, $linkpath);
    }

    function hardlink($linkpath, $target) {
        return \link($target, $linkpath);
    }

    function readlink($path) {
        return \readlink($path);
    }

    function islink($path) {
        return \is_link($path);
    }

    function isfile($path) {
        return \is_file($path);
    }

    function isdir($path) {
        return \is_dir($path);
    }

    function exists($path) {
        return \file_exists($path);
    }
}

/**
 * Path of File System
 */
namespace fs\path {
    function join() {
        $paths = func_get_args();
        return standard(implode('/', $paths));
    }

    function standard($path) {
        $path = trim(str_replace('\\', '/', $path));
        $path = str_replace('://', chr(1), $path);
        $path = preg_replace('#/{2,}#', '/', $path);
        return str_replace(chr(1), '://', $path);
    }

    function basename($path) {
        return \basename($path);
    }

    function name($path) {
        return preg_replace('#\.[^\.]*$#', '', basename($path));
    }

    function entityname($path) {
        return basename($path);
    }

    function subfix($path, $includedot=true) {
        $base = entityname($path);
        $pos = strripos($base, '.');

        if(false == $pos) {
            return '';
        }

        $subfix = substr($base, $pos);
        return $includedot ? $subfix : ltrim($subfix, '.');
    }

    function parent($path, $deep=1) {
        for($i=0; $i<$deep; $i++) {
            $path = dirname($path);
        }

        return $path;
    }
}

/**
 * Input or Output stream
 */
namespace io {

    function writefile($path, $content, $rewrite=false) {
        return file_put_contents($path, $content, $rewrite ? 0 : FILE_APPEND);
    }

    function readfile($path) {
        return file_get_contents($path);
    }

    function screenoutput($content) {

    }

    function screeninput() {

    }
}