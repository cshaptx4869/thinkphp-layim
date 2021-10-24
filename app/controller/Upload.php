<?php

declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use Fairy\Toolkit;
use think\facade\Filesystem;

class Upload extends BaseController
{
    public function image()
    {
        $files = $this->request->file();
        try {
            $this->validate($files, ['file' => 'filesize:10240000|image']);
            $savename = Filesystem::disk('public')->putFile('image', $files['file']);

            $savename = str_replace('\\', '/', $savename);

            return json(Toolkit::success(['src' => '/storage/' . $savename]));
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }
    }

    public function file()
    {
        $files = $this->request->file();
        try {
            $this->validate($files, ['file' => 'filesize:10240000']);
            $savename = Filesystem::disk('public')->putFile('file', $files['file']);
            $savename = str_replace('\\', '/', $savename);
            $filename = $files['file']->getOriginalName();

            return json(Toolkit::success([
                'src' => '/storage/' . $savename, //文件url
                'name' => $filename //文件名
            ]));
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }
    }
}
