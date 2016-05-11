<?php

/**
 * Created by PhpStorm.
 * User: kuangzhiqiang
 * Date: 2016/2/16 0016
 * Time: 15:21
 */
class CaptchaController extends ApplicationController
{

    public function startAction()
    {
        // 极验验证码
        $geetest_conf = $this->getConfig()->geetest;
        $GtSdk = new GeetestLib($geetest_conf->captcha_id, $geetest_conf->private_key);
        $this->session->set("geetest_server", $GtSdk->pre_process());
        echo $GtSdk->get_response_str();
    }

}