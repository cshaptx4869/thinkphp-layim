thinkphp-layim
===============

## 前言

**`Thinkphp6 + GatewayWorker3 + Layim3`** 实现类 QQ 聊天功能。

![](https://images.gitee.com/uploads/images/2021/1224/141608_95c0910d_5507348.png)

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

![](https://images.gitee.com/uploads/images/2021/1224/141021_3fe409d5_5507348.png)

- 申请加好友

![](https://images.gitee.com/uploads/images/2021/1224/141205_48c018d8_5507348.png)

- 同意加好友申请

![](https://images.gitee.com/uploads/images/2021/1224/141259_f27ef315_5507348.png)

- 和好友聊天

![](https://images.gitee.com/uploads/images/2021/1224/141325_aad753c0_5507348.png)

- 申请加群

![](https://images.gitee.com/uploads/images/2021/1224/141350_88671695_5507348.png)

- 消息盒子通知

![](https://images.gitee.com/uploads/images/2021/1224/141411_15f65da7_5507348.png)

- 同意加群申请

![](https://images.gitee.com/uploads/images/2021/1224/141428_11540ad9_5507348.png)

- 系统通知

![](https://images.gitee.com/uploads/images/2021/1224/141444_a97f4090_5507348.png)

- 群聊

![](https://images.gitee.com/uploads/images/2021/1224/141458_4896f6a2_5507348.png)



## 特别感谢：

[Workerman](https://www.workerman.net/)

[Layui](https://www.layui.com/)

