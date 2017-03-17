<?php

namespace Bat;



use CopyDir\AuthorCopyDirUtil;

class FileSystemTool
{

    /**
     * Removes an entry from the filesystem.
     * The entry can be:
     *
     * - a link, then the link only is removed (not the target)
     * - a file, then the file is removed
     * - a directory, the the directory is removed recursively
     *
     * It is considered a success when the entry doesn't exist on the filesystem at the end,
     * and a failure otherwise.
     *
     * By default, the method throws an exception in case of failure.
     *
     * If you set the throwEx flag to false, then this method will return true in case of success,
     * and false in case of failure.
     *
     */
    public static function remove($file, $throwEx = true)
    {
        if (false === is_link($file)) {
            if (file_exists($file)) {
                return self::_remove($file, $throwEx);
            } else {
                return true;
            }
        } else {
            if (false === unlink($file)) {
                return self::_oops("Cannot remove link $file", $throwEx);
            }
            return true;
        }
    }

    /**
     * Copies a directory to a given location.
     */
    public static function copyDir($src, $target, $preservePerms = false, &$errors = [])
    {
        $o = AuthorCopyDirUtil::create();
        $o->setPreservePerms($preservePerms);
        $ret = $o->copyDir($src, $target);
        $errors = $o->getErrors();
        return $ret;
    }




    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private static function _oops($m, $throwEx = true)
    {
        if (true === $throwEx) {
            throw new \Exception($m);
        }
        return false;
    }

    private static function _remove($file, $throwEx = true)
    {
        if (is_dir($file) && !is_link($file)) {
            if (is_readable($file)) {
                $files = new \FilesystemIterator($file,
                    \FilesystemIterator::KEY_AS_PATHNAME |
                    \FilesystemIterator::CURRENT_AS_FILEINFO |
                    \FilesystemIterator::SKIP_DOTS
                );
                foreach ($files as $f) {
                    self::_remove($f, $throwEx);
                }
                if (false === rmdir($file)) {
                    return self::_oops("Cannot remove dir $file", $throwEx);
                }
                return true;
            } else {
                return self::_oops("Cannot remove unreadable dir $file", $throwEx);
            }
        } else {
            if (true === is_file($file) || true === is_link($file)) {
                if (false === unlink($file)) {
                    if (true === is_link($file)) {
                        return self::_oops("Cannot remove link $file", $throwEx);
                    }
                    return self::_oops("Cannot remove file $file", $throwEx);
                }
                return true;
            }
        }
    }
}
