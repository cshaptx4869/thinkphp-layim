<?php
declare (strict_types=1);

namespace app\middleware;

use think\Request;

class Auth
{
    protected $whitelist = [
        'login/checkin',
        'login/captcha',
    ];

    /**
     * 处理请求
     * @param Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Redirect
     */
    public function handle($request, \Closure $next)
    {
        $userInfo = session('userInfo');
        $requestPath = strtolower($request->controller() . '/' . $request->action());
        if ((!in_array($requestPath, $this->whitelist)) && empty($userInfo)) {
            return redirect('login/checkin');
        }
        if ($requestPath === 'login/checkin' && $userInfo) {
            return redirect('/');
        }

        return $next($request);
    }
}
