/** layuiAdmin.std-v1.4.0 LPPL License By https://www.layui.com/admin/ */
;layui.define(function (e) {
    var i = (layui.$, layui.layer, layui.laytpl, layui.setter, layui.view, layui.admin);
    i.events.logout = function () {
        i.req({
            url: '/login/signOut', type: "get", data: {}, done: function (e) {
                i.exit(function () {
                    window.location.href = "/login/signIn"
                })
            }
        })
    }, e("common", {})
});
