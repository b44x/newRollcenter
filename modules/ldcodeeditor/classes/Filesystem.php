<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class Filesystem
{
    protected $base;

    protected function real($path)
    {
        $temp = realpath($path);
        if (!$temp) {
            throw new Exception('Path does not exist: ' . $path);
        }
        if ($this->base && Tools::strlen($this->base)) {
            if (strpos($temp, $this->base) !== 0) {
                throw new Exception('Path is not inside base (' . $this->base . '): ' . $temp);
            }
        }

        return $temp;
    }

    protected function path($id)
    {
        $id = str_replace('/', DIRECTORY_SEPARATOR, $id);
        $id = trim($id, DIRECTORY_SEPARATOR);
        $id = $this->real($this->base . DIRECTORY_SEPARATOR . $id);

        return $id;
    }

    protected function id($path)
    {
        $path = $this->real($path);
        $path = Tools::substr($path, Tools::strlen($this->base));
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $path = trim($path, '/');

        return Tools::strlen($path) ? $path : '/';
    }

    public function __construct($base)
    {
        $this->base = $this->real($base);
        if (!$this->base) {
            throw new Exception('Base directory does not exist');
        }
    }

    public function lst($id, $with_root = false)
    {
        $dir = $this->path($id);
        $lst = @scandir($dir);
        if (!$lst) {
            throw new Exception('Could not list path: ' . $dir);
        }
        $res = [];
        foreach ($lst as $item) {
            if ($item == '.' || $item == '..' || $item === null) {
                continue;
            }
            $tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
            if ($tmp === false || $tmp === 1) {
                continue;
            }
            if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
                $res[] = [
                    'text' => $item,
                    'children' => true,
                    'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item),
                    'icon' => 'folder',
                ];
            } else {
                $res[] = [
                    'text' => $item,
                    'children' => false,
                    'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item),
                    'type' => 'file',
                    'icon' => 'file file-' . Tools::substr($item, strrpos($item, '.') + 1)];
            }
        }
        if ($with_root && $this->id($dir) === '/') {
            $res = [[
                'text' => basename($this->base),
                'children' => $res, 'id' => '/',
                'icon' => 'folder',
                'state' => ['opened' => true, 'disabled' => true],
            ]];
        }

        return $res;
    }

    public function data($id)
    {
        if (strpos($id, ':')) {
            $id = array_map([$this, 'id'], explode(':', $id));

            return ['type' => 'multiple', 'content' => 'Multiple selected: ' . implode(' ', $id)];
        }
        $dir = $this->path($id);
        if (is_dir($dir)) {
            return ['type' => 'folder', 'content' => $id];
        }
        if (is_file($dir)) {
            $ext = strpos($dir, '.') !== false ? Tools::substr($dir, strrpos($dir, '.') + 1) : '';
            $dat = ['type' => $ext, 'content' => ''];
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    $dat['imgurl'] = $dir;
                    break;
                default:
                    $dat['content'] = Tools::file_get_contents($dir);
                    break;
            }
            $dat['path'] = $dir;
            $dat['uid'] = md5($dir);
            $dat['id'] = $id;
            $dat['filename'] = basename($dir);

            return $dat;
        }
        throw new Exception('Not a valid selection: ' . $dir);
    }

    public function create($id, $name, $mkdir = false)
    {
        $dir = $this->path($id);
        if (preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !Tools::strlen($name)) {
            throw new Exception('Invalid name: ' . $name);
        }
        if ($mkdir) {
            mkdir($dir . DIRECTORY_SEPARATOR . $name);
        } else {
            file_put_contents($dir . DIRECTORY_SEPARATOR . $name, '');
        }

        return ['id' => $this->id($dir . DIRECTORY_SEPARATOR . $name)];
    }

    public function rename($id, $name)
    {
        $dir = $this->path($id);
        if ($dir === $this->base) {
            throw new Exception('Cannot rename root');
        }
        if (preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !Tools::strlen($name)) {
            throw new Exception('Invalid name: ' . $name);
        }
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        array_pop($new);
        array_push($new, $name);
        $new = implode(DIRECTORY_SEPARATOR, $new);
        if ($dir !== $new) {
            if (is_file($new) || is_dir($new)) {
                throw new Exception('Path already exists: ' . $new);
            }
            rename($dir, $new);
        }

        return ['id' => $this->id($new)];
    }

    public function remove($id)
    {
        $dir = $this->path($id);
        if ($dir === $this->base) {
            throw new Exception('Cannot remove root');
        }
        if (is_dir($dir)) {
            foreach (array_diff(scandir($dir), ['.', '..']) as $f) {
                $this->remove($this->id($dir . DIRECTORY_SEPARATOR . $f));
            }
            rmdir($dir);
        }
        if (is_file($dir)) {
            unlink($dir);
        }

        return ['status' => 'OK'];
    }

    public function save($id, $content)
    {
        $dir = $this->path($id);
        if ($dir === $this->base) {
            throw new Exception('Cannot save root');
        }
        if (is_dir($dir)) {
            throw new Exception('Cannot save a folder');
        }
        if (is_file($dir)) {
            file_put_contents($dir, $content);
        }

        return ['status' => 'OK'];
    }

    public function move($id, $par)
    {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        rename($dir, $new);
        $dat = [];
        $dat['path'] = $new;
        $dat['id'] = $this->id($new);
        $dat['uid'] = md5($new);
        $dat['oldPath'] = $dir;
        $dat['oldUid'] = md5($dir);

        return $dat;
    }

    public function copy($id, $par)
    {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        if (is_file($new) || is_dir($new)) {
            throw new Exception('Path already exists: ' . $new);
        }

        if (is_dir($dir)) {
            mkdir($new);
            foreach (array_diff(scandir($dir), ['.', '..']) as $f) {
                $this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
            }
        }
        if (is_file($dir)) {
            copy($dir, $new);
        }

        return ['id' => $this->id($new)];
    }
}
