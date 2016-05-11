<?php
/*
 * Mi Framework
 *
 * Copyright (C) 2015 by kuangzhiqiang. All rights reserved
 *
 * To contact the author write to {@link mailto:kuangzhiqiang@xiaomi.com}
 *
 * @author kuangzhiqiang
 * @encoding UTF-8
 * @version $Id: Cookie.php, v 1.0 2015-3-16 11:11:07
 */

/**
 * Description of ErrorCode
 *
 * @author kuangzhiqiang
 */
final class ErrorCode
{

    // 成功
    const SUCCESS           = 0;
    // 系统错误
    const SYSTEM_ERROR      = 500;
    // 页面不存在
    const PAGE_NOTFOUND     = 404;
    // 权限不足
    const PERMISSION_DENIED = 40001;
    // 无效的参数
    const INVALID_PARAMETER = 40002;
    // 无效的格式
    const INVALID_FORMAT    = 40003;
    // 无效的格式
    const INVALID_CAPTCHA   = 40004;
    // 用户不存在
    const USER_NOTFOUND     = 50004;

}
