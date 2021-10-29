thinkphp-layim
===============

## 前言

**`Thinkphp6 + GatewayWorker3 + Layim3`** 实现类 QQ 聊天功能。

![work-with-other-mvc-framework.png](https://i.loli.net/2021/10/24/gpYb1iGeZDNE7Iw.png)

**总体原则:**

- 现有 mvc 框架项目与 GatewayWorker 可以**独立**部署互不干扰
- **所有**的业务逻辑都由网站页面 post/get 到 mvc 框架中完成
- GatewayWorker 不接受客户端发来的数据，即 GatewayWorker 不处理任何业务逻辑，GatewayWorker 仅仅当做一个**单向**的推送通道
- 仅当 mvc 框架需要向浏览器主动推送数据时才在 mvc 框架中调用 Gateway 的 API [GatewayClient](https://github.com/walkor/GatewayClient) 完成推送



> **注意**：layim 商用的话，请去 layui 官网获取授权。



## 任务使用方法

1. 进入项目根目录 
2. 执行命令 **`composer install`**
3. 拷贝 **`.example.env`** 文件为 **`.env`**，并配置正确的数据库
4. 导入数据表 **`database/chat.sql`**
5. 执行命令 **`php think run -p 8888`**，启动内置服务器。语法参照 [thinkphp6](https://www.kancloud.cn/manual/thinkphp6_0/1037479) 手册
6. **windows** 环境双击 **`start_for_win.bat`** 或者 **linux**环境执行命令 **`php start_for_linux.php start`**
7. 访问后台 **`http://127.0.0.1:8888`**
8. 输入账号登录（ 测试账号 `cshaptx4869`、`xianxin`。密码都是 123456。当然也可以自己注册 ）

> **`Applications/Layim/config.php`** 可修改 GatewayWorker 的配置
>
> **`app/controller/Chat.php`** 可修改请求的 WebSocket 地址



## win 下效果展示

- 双击启动脚本

![Snipaste_2021-10-25_11-52-37.png](https://i.loli.net/2021/10/25/8t6yUrgbdl5DxIH.png)

- 申请加好友

![申请加好友.png](https://i.loli.net/2021/10/29/CURMIFcj36qZ4eG.png)

- 同意加好友申请

![同意加好友.png](https://i.loli.net/2021/10/29/3mnrZquCDs5ykSo.png)

- 和好友聊天

![聊天.png](https://i.loli.net/2021/10/29/btTBvzKGaMY7JSO.png)

- 申请加群

![加群请求.png](https://i.loli.net/2021/10/29/aJ4SIHDAx9ynFNK.png)

- 消息盒子通知

![好友加群提示.png](https://i.loli.net/2021/10/29/NOxJM6j94iodQmL.png)

- 同意加群申请

![同意加群.png](https://i.loli.net/2021/10/29/SYo8QeBUxaz2TJC.png)

- 系统通知

![好友同意通知.png](https://i.loli.net/2021/10/29/iwxdDAZRrqblFJI.png)

- 群聊

![群聊1.png](https://i.loli.net/2021/10/29/1PJVea3Qy68g7cl.png)



## 特别感谢：

[Workerman](https://www.workerman.net/)

[Layui](https://www.layui.com/)

