<?php
/**
 * File System
 */
namespace fs {

    use function fs\path\parent;
    use function fs\path\standard;

    function mkdir($path, $mkparent=false) {
        if(!exists($path)) {
            return \mkdir($path, 0777, $mkparent);
        }

        return true;
    }

    function subdirs($dir) {
        $result = [];

        foreach(array_diff(scandir($dir), ['.', '..']) as $sub) {
            if(isdir(path\join($dir, $sub))) {
                $result []= $sub;
            }
        }

        return $result;
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
        if (islink($path)) {
            if(PHP_OS == 'WINNT') {
                try{
                    return rmfile($path);
                } catch (\Exception $e) {
                    $output = [];
                    $result = 2;
                    exec(sprintf('rmdir "%s" 2>&1', $path), $output, $result);

                    if($result == 0) {
                        return true;
                    } else {
                        throw new \Exception(iconv('GB2312', 'UTF-8', $output[0]));
                    }
                }
            } else {
                return rmfile($path);
            }
        } else if(isfile($path)) {
            return rmfile($path);
        } else if(isdir($path)) {
            return rmdir($path, $rmtree);
        } else {
            return false;
        }
    }

    function symlink($linkpath, $target) {
        if(PHP_OS == 'WINNT') {
            $params = '';

            if(isdir($target)) {
                $params = '/D';
            }

            $linkpath = str_replace('/', '\\', $linkpath);
            $target = str_replace('/', '\\', $target);
            $output = [];
            $result = 2;
            exec(sprintf('mklink %s "%s" "%s" 2>&1', $params, $linkpath, $target), $output, $result);
            return $result == 0 ? true : iconv('GB2312', 'UTF-8', $output[0]);
        }

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

    function copy($source, $target) {
        if(isdir($source)) {
            $dir = opendir($source);
            @mkdir($target);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($source . '/' . $file) ) {
                        copy($source . '/' . $file,$target . '/' . $file);
                    }
                    else {
                        \copy($source . '/' . $file,$target . '/' . $file);
                    }
                }
            }
            closedir($dir);
        } else {
            \copy($source, $target);
        }
    }
}

/**
 * Path of File System
 */
namespace fs\path {
    function join() {
        $paths = array_filter(func_get_args());
        return standard(implode('/', $paths));
    }

    function standard($path) {
        $path = trim(str_replace('\\', '/', $path));
        $path = str_replace('://', chr(1), $path);
        $path = preg_replace('#/{2,}#', '/', $path);
        $path = rtrim(str_replace(chr(1), '://', $path), '/');
        return $path == '' ? '/' : $path;
    }

    function subpath($parent, $sub) {
        $parent = standard($parent);
        $sub = standard($sub);
        $subpath = substr($sub, strlen($parent));

        return $subpath[0] == '/' ? ltrim($subpath, '/') : false;
    }

    function relative($path, $targetPath) {
        $path = standard($path);
        $targetPath = standard($targetPath);
        $mary = explode('/', $path);
        $tary = explode('/', $targetPath);
        $mcount = count($mary);

        $sameHead = \ary\samehead($mary, $tary);
        $scount = count($sameHead);
        $result = [];

        if(empty($sameHead)) {
            return $targetPath;
        }

        if($mcount > $scount) {
            $result =  explode('/', str_repeat('../', $mcount - $scount));
        }

        $tmp = $tary;

        array_splice($tmp, 0, $scount);
        $result = array_filter(\ary\concat($result, $tmp));
        return implode( '/', $result);
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
}

namespace ary {
    function samehead($ary1, $ary2) {
        $ary = [];
        $ary1 = array_values($ary1);
        $ary2 = array_values($ary2);

        for($i=0; $i<count($ary1); $i++) {
            if(!isset($ary2[$i]) || $ary1[$i] != $ary2[$i]) {
                return $ary;
            }

            $ary []= $ary1[$i];
        }

        return $ary;
    }

    function expand($ary) {
        $result = [];

        foreach($ary as $a) {
            if(is_array($a)) {
                $result = concat($result, $a);
            } else {
                $result []= $a;
            }
        }
    }

    function concat()
    {
        $arys = func_get_args();
        $result = $arys[0];
        $count = count($arys);

        for($i=1; $i<$count; $i++) {
            foreach($arys[$i] as $value) {
                $result []= $value;
            }
        }

        return $result;
    }
}

namespace str {
    function matchwildcard($subject, $wildcard, $ignorecase=false) {
        return preg_match(wildcardtoregular($wildcard, $ignorecase), $subject);
    }

    function wildcardtoregular($wildcard, $ignorecase=false) {
        static $_cache;

        if(isset($_cache[$ignorecase][$wildcard])) {
            return $_cache[$ignorecase][$wildcard];
        }

        $option = '';

        if($ignorecase) {
            $option = 'i';
        }

        $wildcard = str_replace('*', chr(1), $wildcard);
        $wildcard = str_replace('?', chr(2), $wildcard);
        $wildcard = preg_quote($wildcard);
        $wildcard = str_replace(chr(1), '.*', $wildcard);
        $wildcard = str_replace(chr(2), '.', $wildcard);
        return $_cache[$ignorecase][$wildcard] = "#^$wildcard$#$option";
    }

    function stringformat($ary, $isAssoc=false)
    {
        if($isAssoc) {
            $strAry = [];
            $keys = array_keys($ary);
            $max = 0;

            foreach($keys as $key) {
                $len = strlen($key);

                if($max < $len) {
                    $max = $len;
                }
            }

            foreach($ary as $key => $value) {
                $strAry []= sprintf("%{$max}s: %s", $key, $value);
            }

            return implode("\n", $strAry);
        } else {
            return implode("\n", $ary);
        }
    }
}