thinkphp-layim
===============

## 前言

**`Thinkphp6 + GatewayWorker3 + Layim3`** 实现类 QQ 聊天功能。

![work-with-other-mvc-framework.png](https://i.loli.net/2021/10/24/gpYb1iGeZDNE7Iw.png)

> **注意：**layim 商用的话，请去 layui 官网获取授权。



## 任务使用方法

1. 进入项目根目录 
2. 执行命令 **`composer install`**
3. 拷贝 **`.example.env`** 文件为 **`.env`**，并配置正确的数据库
4. 导入数据表 **`database/chat.sql`**
5. 执行命令 **`php think run -p 8888`**，启动内置服务器。语法参照 thinkphp6 手册
6. 双击 **`start_for_win.bat`** (windows环境) 或者 执行命令**`php start_for_linux.php` **(linux环境)
7. 访问后台 **`http://127.0.0.1:8888/admin`**
8. 输入账号登录（ 测试账号 `cshaptx4869`、`xianxin`。密码都是 123456 ）



## win 下效果展示

- 双击启动脚本

![Snipaste_2021-10-24_15-06-43.jpg](https://i.loli.net/2021/10/24/hsCLfwVbcmIRjyX.jpg)

- 账号 `cshaptx4869` 登录后效果

![2.jpg](https://i.loli.net/2021/10/24/zxAYadISGJkbriQ.jpg)

- 向账号 `xianxin` 发起聊天

![3.jpg](https://i.loli.net/2021/10/24/E1nRqpBr4OjHUCa.jpg)

- 账号 `xianxin` 收到消息提示

![4.jpg](https://i.loli.net/2021/10/24/RCTHj5b3FOoS2yv.jpg)

- 账号 `xianxin` 点开消息面板

![4jpg.jpg](https://i.loli.net/2021/10/24/Pxc23Now6rzvIM5.jpg)



## 特别感谢：

[Workerman](https://www.workerman.net/)

[Layui](https://www.layui.com/)

