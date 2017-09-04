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

    /**
     * 判断是否为空目录
     * @param $path string 指定的路径
     * @return bool 非空目录和非目录均返回false
     */
    function isempty($path) {
        if(!is_dir($path)) {
            return false;
        }

        $dir = opendir($path);
        while(false !== ( $file = readdir($dir)) ) {
            if(!in_array($file, ['.', '..'])) {
                closedir($dir);
                return false;
            }
        }

        closedir($dir);
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
            is_dir("$path/$file") ? rmdir("$path/$file", $rmtree) : rmfile("$path/$file");
        }

        return \rmdir($path);
    }

    function rm($path, $rmtree=false) {
        if (islink($path)) {
            if(\console\iswindows()) {
                try{
                    return rmfile($path);
                } catch (\Exception $e) {
                    if(!\console\execwincmd('rmdir', [str_replace("/", '\\', $path)], $output)) {
                        throw \console\exception($output . "\n[Prev Operation Error] {$e->getMessage()})");
                    }

                    return true;
                }
            } else {
                return rmfile($path);
            }
        } else if(isfile($path)) {
            return rmfile($path);
        } else if(isdir($path)) {
            return \fs\rmdir($path, $rmtree);
        } else {
            return false;
        }
    }

    function symlink($linkpath, $target) {
        if(\console\iswindows()) {
            $args = [];

            if(isdir($target)) {
                $args []= '/D';
            }

            $linkpath = str_replace('/', '\\', $linkpath);
            $target = str_replace('/', '\\', $target);
            $args = \ary\concat($args, [$linkpath, $target]);

            if(!\console\execwincmd('mklink', $args, $output)) {
                throw \console\exception($output);
            }

            return true;
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

            if(!isdir($target)) {
                @mkdir($target, true);
            }

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
            $targetDirName = dirname($target);

            if(!isdir($targetDirName)) {
                @mkdir($targetDirName, true);
            }

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

    function absolute($path=null) {
        if(empty($path)) {
            return standard(getcwd());
        }

        $path = standard($path);

        if(!preg_match("#^/|^[^/]:#", $path)) {
            $path = standard(join(getcwd(), $path));
        }

        list($root, $path) = explode('/', $path, 2);
        $root .= '/';

        if(empty($path)) {
            return $root;
        }

        $pathSegment = explode("/", $path);
        $newSegment = [];

        foreach ($pathSegment as $seg) {
            if($seg == '.' or $seg == '') {
                continue;
            }

            if($seg == '..') {
                array_pop($newSegment);
                continue;
            }

            array_push($newSegment, $seg);
        }

        return $root . standard(implode("/", $newSegment));
    }

    function standard($path) {
        $path = trim(str_replace('\\', '/', $path));
        $path = str_replace('://', chr(1), $path);
        $path = preg_replace('#/{2,}#', '/', $path);
        return $path == '/' ? '/' : rtrim(str_replace(chr(1), '://', $path), '/');
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

    function endline() {
        if(\console\iswindows()) {
            return "\r\n";
        } else {
            return "\n";
        }
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

    function maxlength($array)
    {
        $max = 0;

        foreach($array as $ary) {
            $len = strlen((string)$ary);

            if($max <= $len) {
                $max = $len;
            }
        }

        return $max;
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

    function screenoutputformat($ary, $outputCols) {
        $_get_suitable_colcount = function ($ary, $outputCols) {
            $len = 0;
            $i = 0;

            foreach($ary as $val) {
                $vlen = strlen($val);

                if($len + $vlen > $outputCols - 2*($i-1)) {
                    break;
                }

                $i ++;
            }

            return $i < 1 ? 1 : $i;
        };

        $_calc_cols_maxlengths = function ($ary) {
            $maxlength = [];

            foreach($ary as $val) {
                $maxlength []= maxlength($val);
            }

            return $maxlength;
        };

        $colcount = $_get_suitable_colcount($ary, $outputCols-1);

        $maxlengths = [''];

        while($colcount>1) {
            $tmp = towishcolformat($ary, $colcount);
            $maxlengths = $_calc_cols_maxlengths($tmp);

            if(array_sum($maxlengths) > $outputCols-1 - 2*($colcount-1)) {
                $colcount --;
            } else {
                break;
            }
        }

        return ['lengths' => $maxlengths, 'data' => coltorowformat($ary, $colcount, true)];
    }

    function coltorowformat($ary, $colcount, $fillMissing = false) {
        $i = 0;
        $resultAry = [];
        $count = 0;

        foreach ($ary as $val) {
            $count ++ ;
            $resultAry [$i] []= $val;

            if($count >= $colcount) {
                $i ++;
                $count = 0;
            }
        }

        if($fillMissing) {
            while($count<$colcount) {
                $resultAry [$i] []= '';
                $count ++;
            }
        }

        return $resultAry;
    }

    function towishcolformat($ary, $colcount, $fillMissing = false) {
        $resultAry = [];
        $i = 0;

        foreach($ary as $val) {
            $resultAry [$i % $colcount] []= $val;
            $i ++;
        }

        if($fillMissing) {
            while($i % $colcount != 0) {
                $resultAry [$i % $colcount] []= '';
                $i ++;
            }
        }

        return $resultAry;
    }
}

namespace str {

    use function ary\maxlength;

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

namespace console {
    function cols($default=80) {
        if(\console\iswindows()) {
            if(execwincmd('mode con', null, $output)) {
                preg_match("#^.*?:\s*\-+\s*.*\s*.*?:.*?([0-9]+)#",trim($output), $matchs);
                $cols = intval($matchs[1]);

                if(!$cols) {
                    return $default;
                }

                return $cols;
            }
        }

        return $default;
    }

    function execwincmddirectly($cmd, $args=[], $unused=null) {
        $cmd = implode(' ', [$cmd, packargs($args), ' 2>&1']);
        system($cmd);
    }

    function execwincmdresult($cmd, $args=[], $unused=null) {
        execwincmd($cmd, $args, $output);
        return $output;
    }

    function execwincmd($cmd, $args=[], &$output=null, $convert=true) {
        if(iswindows()) {
            $cmd = implode(' ', [$cmd, packargs($args), ' 2>&1']);
            exec($cmd, $output, $result);

            if($result == 0) {
                if($convert) {
                    $output = iconv('GB2312', 'UTF-8', implode(\io\endline(), $output));
                } else {
                    $output = implode(\io\endline(), $output);
                }
            } else {
                if($convert) {
                    $output = iconv('GB2312', 'UTF-8', $output[0]);
                } else {
                    $output = $output[0];
                }
                $output .= " ($cmd)";
            }

            return $result == 0;
        }

        return false;
    }

    function iswindows(){
        return strtoupper(PHP_OS) == "WINNT";
    }

    function packargs($args) {
        $args = empty($args) ? [] : $args;

        foreach($args as $i => $arg) {
            if(strpos($arg," ")) {
                $args[$i] = "\"$arg\"";
            }
        }

        return implode(' ', $args);
    }

    function exception($msg, $code=2) {
        return new \Exception($msg, $code);
    }
}