layui.define(['jquery', 'layer'], function (exports) {
    var MODULE_NAME = 'toolkit',
        config = {
            shade: [0.02, '#000']
        },
        $ = layui.jquery,
        layer = layui.layer;

    var toolkit = {
        msg: {
            // 成功消息
            success: function (msg, callback) {
                if (callback === undefined) {
                    callback = function () {
                    }
                }
                return layer.msg(msg, {
                    icon: 1,
                    shade: config.shade,
                    scrollbar: false,
                    time: 2000,
                    shadeClose: true
                }, callback);
            },
            // 失败消息
            error: function (msg, callback) {
                if (callback === undefined) {
                    callback = function () {
                    }
                }
                return layer.msg(msg, {
                    icon: 2,
                    shade: config.shade,
                    scrollbar: false,
                    time: 3000,
                    shadeClose: true
                }, callback);
            },
            // 警告消息框
            alert: function (msg, callback) {
                return layer.alert(msg, {end: callback, scrollbar: false});
            },
            // 对话框
            confirm: function (msg, ok, no) {
                layer.confirm(msg, {title: '操作确认', btn: ['确认', '取消']}, function (index) {
                    typeof ok === 'function' && ok.call(this);
                    toolkit.msg.close(index);
                }, function (index) {
                    typeof no === 'function' && no.call(this);
                    toolkit.msg.close(index);
                });
            },
            // 消息提示
            tips: function (msg, time, callback) {
                return layer.msg(msg, {
                    time: (time || 3) * 1000,
                    shade: config.shade,
                    end: callback,
                    shadeClose: true
                });
            },
            // 加载中提示
            loading: function (msg, callback) {
                return msg ? layer.msg(msg, {
                    icon: 16,
                    scrollbar: false,
                    shade: config.shade,
                    time: 0,
                    end: callback
                }) : layer.load(2, {time: 0, scrollbar: false, shade: config.shade, end: callback});
            },
            // 关闭消息框
            close: function (index) {
                return layer.close(index);
            }
        },
        request: {
            post: function (option, ok, no, ex) {
                return this.ajax('post', option, ok, no, ex);
            },
            get: function (option, ok, no, ex) {
                return this.ajax('get', option, ok, no, ex);
            },
            ajax: function (type, option, ok, no, ex) {
                type = type || 'get';
                option.url = option.url || '';
                option.data = option.data || {};
                option.prefix = option.prefix || false;
                option.statusName = option.statusName || 'code';
                option.statusCode = option.statusCode || 0;
                ok = ok || function (res) {
                };
                no = no || function (res) {
                    toolkit.msg.error(res.msg == undefined ? '返回数据格式有误' : res.msg);
                    return false;
                };
                ex = ex || function (res) {
                };
                if (option.url == '') {
                    toolkit.msg.error('请求地址不能为空');
                    return false;
                }
                $.ajax({
                    url: option.url,
                    type: type,
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    dataType: "json",
                    data: option.data,
                    timeout: 60000,
                    success: function (res) {
                        if (res[option.statusName] == option.statusCode) {
                            return ok(res);
                        } else {
                            return no(res);
                        }
                    },
                    error: function (xhr, textstatus, thrown) {
                        toolkit.msg.error('Status:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！', function () {
                            ex(this);
                        });
                        return false;
                    }
                });
            }
        }
    };

    exports(MODULE_NAME, toolkit);
})
