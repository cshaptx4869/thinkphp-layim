<?php

namespace app\controller;

use app\BaseController;
use app\model\Member;
use Fairy\Toolkit;

class Login extends BaseController
{
    /**
     * 登录
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkin()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post(['username', 'password', 'captcha']);
            $rule = [
                'username|用户名' => 'require',
                'password|密码' => 'require',
                'captcha|验证码' => ['require', 'captcha'],
            ];
            try {
                $this->validate($post, $rule);
            } catch (\Exception $e) {
                return json(Toolkit::error($e->getMessage()));
            }

            $memberInfo = Member::where('account', $post['username'])->find();
            if (!$memberInfo) {
                return json(Toolkit::error('用户不存在'));
            }
            if (md5($post['password'] . $memberInfo->salt) !== $memberInfo->password) {
                return json(Toolkit::error('用户名或密码错误'));
            }
            session('userInfo', [
                'id' => $memberInfo->id,
                'username' => $memberInfo->username
            ]);

            return json(Toolkit::success('', '登录成功'));
        }

        return view();
    }

    public function reg()
    {
        return view();
    }

    public function forget()
    {
        return view();
    }
}
