<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\controller\SystemConfig;
use com\verify\HonrayVerify;
use think\facade\Request;

class Base extends Common
{

    public function login()
    {
        $userModel = model('User');

        $param = $this->param;
        $username = $param->username;
        $password = $param->password;
        $verifyCode = !empty($param->verifyCode) ? $param->verifyCode : '';
        $isRemember = !empty($param->isRemember) ? $param->isRemember : '';
        $data = $userModel->login($username, $password, $verifyCode, $isRemember);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    public function relogin()
    {
        $userModel = model('User');
        $param = $this->param;

        if (empty($param->rememberKey)) {
            return resultArray(['error' => '校验信息失败，重登陆失败。']);
        }
        $data = decrypt($param->rememberKey);
        $username = $data['username'];
        $password = $data['password'];

        $data = $userModel->login($username, $password, '', true, true);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    public function logout()
    {
        $param = $this->param;
        cache('Auth_' . $param->authkey, null);
        return resultArray(['data' => '退出成功']);
    }

    public function getVerify()
    {
        $captcha = new HonrayVerify(config('captcha'));
        return $captcha->entry();
    }

    public function setInfo()
    {
        $userModel = model('User');
        $param = $this->param;
        $old_pwd = $param->old_pwd;
        $new_pwd = $param->new_pwd;
        $auth_key = $param->auth_key;
        $data = $userModel->setInfo($auth_key, $old_pwd, $new_pwd);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    // miss 路由：处理没有匹配到的路由规则
    public function miss()
    {
        if (Request::instance()->isOptions()) {
            return;
        } else {
            echo 'vuethink接口[TP5.1]';
        }
    }
}
