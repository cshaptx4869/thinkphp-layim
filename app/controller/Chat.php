<?php

namespace app\controller;

use app\BaseController;
use app\model\Friend;
use app\model\FriendGroup;
use app\model\Group;
use app\model\GroupMember;
use app\model\Member;
use app\model\Msgbox;
use app\model\Record;
use app\model\Skin;
use Fairy\Toolkit;
use GatewayClient\Gateway;
use think\facade\Db;

class Chat extends BaseController
{
    const SOCKET_NAME = 'ws://127.0.0.1:7272';
    const REGISTER_ADDRESS = '127.0.0.1:1236';

    //事件
    const EMIT_GET_MESSAGE = 'getMessage';
    const EMIT_SET_FRIEND_STATUS = 'setFriendStatus';
    const EMIT_MSGBOX = 'msgbox';
    const EMIT_ADD_LIST = 'addList';

    public function initialize()
    {
        parent::initialize();
        Gateway::$registerAddress = self::REGISTER_ADDRESS;
    }

    /**
     * 展示页面
     * @return \think\response\View
     */
    public function index()
    {
        $skin = Skin::where('member_id', $this->userInfo['id'])
            ->value('filename', '');

        return view('', [
            'socket_name' => self::SOCKET_NAME,
            'skin' => $skin
        ]);
    }

    /**
     * 连接绑定
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function bind()
    {
        $post = $this->request->post();
        $rule = [
            'client_id' => 'require'
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }

        Gateway::bindUid($post['client_id'], $this->userInfo['id']);
        //通知朋友我已上线
        $memberObj = Member::find($this->userInfo['id']);
        $memberObj->status == Member::STATUS_ONLINE && $this->notifyFriendOnlineStatus();
        //消息盒子通知
        Gateway::sendToUid($this->userInfo['id'], $this->makeMessage(self::EMIT_MSGBOX, [
            'count' => Msgbox::getUnreadCountByMemberId($this->userInfo['id'])
        ]));

        return json(Toolkit::success('绑定成功'));
    }

    /**
     * 下线通知
     * @throws \think\db\exception\DbException
     */
    public function offline()
    {
        $this->notifyFriendOnlineStatus(false);
    }

    /**
     * 初始化
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function init()
    {
        $memberInfo = Member::find($this->userInfo['id']);
        $mineData = [
            'username' => $memberInfo->nickname,//我的昵称
            'id' => $memberInfo->id,//我的ID
            'status' => Member::getStatusText($memberInfo->status),//在线状态 online：在线、hide：隐身
            'sign' => $memberInfo->signature,//我的签名
            'avatar' => $memberInfo->avatar//我的头像
        ];

        //好友列表
        $friendData = [];
        $friendGroupArr = FriendGroup::where('member_id', $this->userInfo['id'])
            ->field('id,group_name')
            ->order('weight', 'desc')
            ->select()
            ->toArray();
        $groupIds = array_column($friendGroupArr, 'id');
        if ($groupIds) {
            //用户朋友组中的用户信息
            $groupFriends = Db::name('friend')->alias('f')
                ->join(['chat_member' => 'm'], 'f.member_id = m.id')
                ->whereIn('f.group_id', $groupIds)
                ->field('f.group_id,f.nickname remarkNickname,f.member_id,m.nickname,m.signature,m.avatar,m.status')
                ->select()
                ->toArray();
            foreach ($friendGroupArr as $friendGroup) {
                $tmp = [
                    'groupname' => $friendGroup['group_name'],//好友分组名
                    'id' => $friendGroup['id'],//分组ID
                    'list' => [],//分组下的好友列表
                ];

                foreach ($groupFriends as $k => $groupFriend) {
                    if ($groupFriend['group_id'] == $friendGroup['id']) {
                        $tmp['list'][] = [
                            'username' => $groupFriend['remarkNickname'] ?: $groupFriend['nickname'],//好友昵称
                            'id' => $groupFriend['member_id'],//好友ID
                            'avatar' => $groupFriend['avatar'],//好友头像
                            'sign' => $groupFriend['signature'],//好友签名
                            'status' => Member::getStatusText($memberInfo->status)//若值为offline代表离线，online或者不填为在线
                        ];
                        unset($groupFriends[$k]);
                    }
                }
                $friendData[] = $tmp;
            }
        }

        //群组列表
        $groupData = [];
        $groupIds = GroupMember::where('member_id', $this->userInfo['id'])
            ->column('group_id');
        if ($groupIds) {
            $groupArr = Group::whereIn('id', $groupIds)
                ->field('id,group_name,avatar')
                ->select();
            foreach ($groupArr as $group) {
                $groupData[] = [
                    'groupname' => $group['group_name'],//群组名
                    'id' => $group['id'],//群组ID
                    'avatar' => $group['avatar'],//群组头像
                ];
            }
        }

        return json(Toolkit::success([
            'mine' => $mineData,
            'friend' => $friendData,
            'group' => $groupData
        ]));
    }

    /**
     * 获取群员列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function members()
    {
        $get = $this->request->get();
        $rule = [
            'id' => 'require'
        ];
        try {
            $this->validate($get, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }

        $groupMembers = Db::name('group_member')->alias('gm')
            ->join(['chat_member' => 'm'], 'm.id=gm.member_id')
            ->where('gm.group_id', $get['id'])
            ->field('gm.nickname remarkNickname,m.id,m.avatar,m.signature,m.nickname nickname')
            ->select();

        $data = [];
        foreach ($groupMembers as $groupMember) {
            $data[] = [
                'username' => $groupMember['remarkNickname'] ?: $groupMember['nickname'],//群员昵称
                'id' => $groupMember['id'],//群员id
                'avatar' => $groupMember['avatar'],//群员头像
                'sign' => $groupMember['signature']//群员签名
            ];
        }

        return json(Toolkit::success(['list' => $data]));
    }

    /**
     * 发送消息
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function sendMessage()
    {
        $post = $this->request->post();
        /**
         * mime 格式
         * {
         *   avatar: "avatar.jpg" //我的头像
         *   ,content: "你好吗" //消息内容
         *   ,id: "100000" //我的id
         *   ,mine: true //是否我发送的消息
         *   ,username: "纸飞机" //我的昵称
         * }
         *
         * to 格式
         * {
         *   avatar: "avatar.jpg"
         *   ,id: "100001"
         *   ,name: "贤心"
         *   ,sign: "这些都是测试数据，实际使用请严格按照该格式返回"
         *   ,type: "friend" //聊天类型，一般分friend和group两种，group即群聊
         *   ,username: "贤心"
         * }
         */
        $mine = $post['mine'];
        $to = $post['to'];

        //聊天记录
        $recordObj = Record::create([
            'sender' => $mine['id'],
            'receiver' => $to['id'],
            'content' => $mine['content'],
            'send_time' => time(),
            'type' => $to['type'],
        ]);

        //朋友消息
        if ($to['type'] === 'friend') {
            if ($mine['id'] !== $to['id']) {
                //好友在线
                if (Gateway::isUidOnline($to['id'])) {
                    $toMemberObj = Member::find($to['id']);
                    Gateway::sendToUid($to['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                        //来自于用户的聊天消息，它必须接受以下字段
                        'username' => $mine['username'],//消息来源用户名
                        'avatar' => $mine['avatar'],//消息来源用户头像
                        'id' => $mine['id'],//消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
                        'type' => $to['type'],//聊天窗口来源类型，从发送消息传递的to里面获取
                        'content' => $mine['content'],//消息内容
                        'cid' => $recordObj->id,//消息id，可不传。除非你要对消息进行一些操作（如撤回）
                        'mine' => false,//是否我发送的消息，如果为true，则会显示在右方
                        'fromid' => $mine['id'],//消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
                        'timestamp' => time() * 1000,//服务端时间戳毫秒数
                    ]));

                    //好友隐身
                    if ($toMemberObj->status == Member::STATUS_HIDE) {
                        Gateway::sendToUid($mine['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                            //来自于系统的聊天面板的消息
                            'system' => true,//系统消息
                            'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                            'type' => $to['type'],//聊天窗口类型
                            'content' => '好友已离线',
                        ]));
                    }
                } else {
                    //好友离线
                    Gateway::sendToUid($mine['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                        //来自于系统的聊天面板的消息
                        'system' => true,//系统消息
                        'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                        'type' => $to['type'],//聊天窗口类型
                        'content' => '好友已离线',
                    ]));
                }
            } else {
                Gateway::sendToUid($to['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                    'system' => true,
                    'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                    'type' => $to['type'],
                    'content' => '不能给自己发送消息',
                ]));
            }
        } else if ($to['type'] === 'group') {
            //群消息
            $groupStatus = Group::where('id', $to['id'])->value('group_status');
            //群禁言
            if ($groupStatus) {
                Gateway::sendToUid($mine['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                    'system' => true,
                    'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                    'type' => $to['type'],
                    'content' => '群全体禁言',
                ]));
            } else {
                $status = GroupMember::where('member_id', $mine['id'])->value('status');
                //用户禁言
                if ($status) {
                    Gateway::sendToUid($mine['id'], $this->makeMessage(self::EMIT_GET_MESSAGE, [
                        'system' => true,
                        'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                        'type' => $to['type'],
                        'content' => '你被管理员禁言',
                    ]));
                } else {
                    //获取正常的群员
                    $memberIds = GroupMember::where('group_id', $to['id'])
                        ->where('member_id', '<>', $mine['id'])
                        ->where('status', GroupMember::STATUS_NORMAL)
                        ->column('member_id');
                    foreach ($memberIds as $memberId) {
                        if (Gateway::isUidOnline($memberId)) {
                            Gateway::sendToUid($memberId, $this->makeMessage(self::EMIT_GET_MESSAGE, [
                                'username' => $mine['username'],
                                'avatar' => $mine['avatar'],
                                'id' => $to['id'],
                                'type' => $to['type'],
                                'content' => $mine['content'],
                                'cid' => $recordObj->id,
                                'mine' => false,
                                'fromid' => $mine['id'],
                                'timestamp' => time() * 1000,
                            ]));
                        }
                    }
                }
            }
        }

        return json(Toolkit::success());
    }

    /**
     * 加入群聊
     * @return \think\response\Json
     */
    public function enterGroup()
    {
        $post = $this->request->post();
        //获取正常的群员
        $memberIds = GroupMember::where('group_id', $post['id'])
            ->where('member_id', '<>', $this->userInfo['id'])
            ->where('status', GroupMember::STATUS_NORMAL)
            ->column('member_id');
        foreach ($memberIds as $memberId) {
            if (Gateway::isUidOnline($memberId)) {
                Gateway::sendToUid($memberId, $this->makeMessage(self::EMIT_GET_MESSAGE, $post));
            }
        }

        return json(Toolkit::success());
    }

    /**
     * 退出
     * @throws \think\db\exception\DbException
     */
    public function close()
    {
        $this->notifyFriendOnlineStatus(false);

        return json(Toolkit::success());
    }

    /**
     * 在线状态切换
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function online()
    {
        $post = $this->request->post();
        $rule = [
            'value' => 'require'
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }

        $memberObj = Member::find($this->userInfo['id']);
        $memberObj->status = Member::getStatusValue($post['value']);
        $memberObj->save();

        $this->notifyFriendOnlineStatus($post['value'] === 'online');

        return json(Toolkit::success());
    }

    /**
     * 修改签名
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function sign()
    {
        $post = $this->request->post();
        Db::name('member')
            ->where('id', $this->userInfo['id'])
            ->update(['signature' => $post['value']]);

        return json(Toolkit::success());
    }

    /**
     * 更换背景皮肤
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setSkin()
    {
        $post = $this->request->post();
        $rule = [
            'value' => 'require'
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }

        $skinObj = Skin::where('member_id', $this->userInfo['id'])->find();
        if (!$skinObj) {
            Skin::create([
                'member_id' => $this->userInfo['id'],
                'filename' => $post['value']
            ]);
        } else {
            $skinObj->filename = $post['value'];
            $skinObj->save();
        }

        return json(Toolkit::success());
    }

    /**
     * 添加好友
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function find()
    {
        if ($this->request->isAjax()) {
            $get = $this->request->get();
            $rule = [
                'type' => 'require',
                'page' => 'require',
                'limit' => 'require'
            ];
            try {
                $this->validate($get, $rule);
            } catch (\Exception $e) {
                return json(Toolkit::error($e->getMessage()));
            }

            if ($get['type'] === 'friend') {
                if ($get['account']) {
                    $qb = Member::whereLike('account', '%' . $get['account'] . '%')
                        ->where('id', '<>', $this->userInfo['id']);
                    $count = $qb->count();
                    $list = $qb->field('id,account,nickname,signature,avatar')
                        ->page($get['page'], $get['limit'])
                        ->select();
                } else {
                    $myFriendGroupIds = FriendGroup::where('member_id', $this->userInfo['id'])
                        ->column('id');
                    $myFriendIds = Friend::whereIn('group_id', $myFriendGroupIds)
                        ->column('member_id');
                    $qb = Member::whereNotIn('id', $myFriendIds)
                        ->where('id', '<>', $this->userInfo['id'])
                        ->field('id,account,nickname,signature,avatar');
                    $count = $qb->count();
                    $list = $qb->order('id', 'desc')
                        ->page($get['page'], $get['limit'])
                        ->select();
                }
            } else if ($get['type'] === 'group') {
                if ($get['account']) {
                    $qb = Group::whereLike('account', '%' . $get['account'] . '%');
                    $count = $qb->count();
                    $list = $qb->field('id,account,group_name nickname,`desc` signature,avatar')
                        ->page($get['page'], $get['limit'])
                        ->select();
                } else {
                    $myGroupIds = GroupMember::where('member_id', '=', $this->userInfo['id'])
                        ->column('group_id');
                    $qb = Group::whereNotIn('id', $myGroupIds);
                    $count = $qb->count();
                    $list = $qb->field('id,account,group_name nickname,`desc` signature,avatar')
                        ->page($get['page'], $get['limit'])
                        ->select();
                }
            }

            return json(Toolkit::success(['list' => $list, 'count' => $count]));
        }

        return view();
    }

    /**
     * 请求加好友
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function makeFriend()
    {
        $post = $this->request->post();
        $rule = [
            'type' => ['require', 'in:friend,group'],
            'to' => 'require',
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }

        $post['from'] = $this->userInfo['id'];
        $post['send_time'] = time();
        if ($post['type'] === 'friend') {
            $post['type'] = Msgbox::TYPE_MAKE_FRIEND_USER;
            $post['content'] = '申请添加你为好友';
        } else {
            $post['type'] = Msgbox::TYPE_JOIN_GROUP_USER;
            $post['group_id'] = $post['to'];
            $groupInfo = Group::find($post['to']);
            $post['to'] = $groupInfo->belong;
            $post['content'] = '申请加入群 [' . $groupInfo->group_name . ']';
        }
        Msgbox::create($post);
        if (Gateway::isUidOnline($post['to'])) {
            Gateway::sendToUid($post['to'], $this->makeMessage(self::EMIT_MSGBOX, [
                'count' => Msgbox::getUnreadCountByMemberId($post['to'])
            ]));
        }

        return json(Toolkit::success());
    }

    /**
     * 消息盒子
     * 消息盒子
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function msgbox()
    {
        if ($this->request->isAjax()) {
            $page = $this->request->get('page', 1);
            $limit = $this->request->get('limit', 10);
            $qb = Msgbox::where('to', $this->userInfo['id']);
            $count = $qb->count();
            $list = $qb->page($page, $limit)
                ->order('id', 'desc')
                ->select()
                ->toArray();
            $data = [];
            if ($list) {
                //发送者信息
                $fromArr = Member::whereIn('id', array_column($list, 'from'))
                    ->fieldRaw("id,account,nickname,avatar,signature,status")
                    ->select()
                    ->toArray();
                $fromArrMap = Toolkit::setArrayIndex($fromArr, 'id');
                $groupArr = Group::whereIn('id', array_column($list, 'group_id'))
                    ->field('id,account,group_name,avatar')
                    ->select()
                    ->toArray();
                $groupArrMap = Toolkit::setArrayIndex($groupArr, 'id');
                foreach ($list as $row) {
                    $userInfo =[];
                    if (isset($fromArrMap[$row['from']])) {
                        $userInfo = $fromArrMap[$row['from']];
                        $userInfo['status'] = Member::getStatusText($userInfo['status']);
                    }
                    $data[] = [
                        'id' => $row['id'],
                        'content' => $row['content'],
                        'to' => $this->userInfo['id'],
                        'from' => $row['from'],
                        'group' => $row['group_id'],
                        'type' => $row['type'],
                        'remark' => $row['remark'],
                        'href' => null,
                        'read' => $row['status'],
                        'time' => Toolkit::formatDate($row['send_time']),
                        'read_time' => date('Y-m-d H:i:s', $row['read_time']),
                        'status' => $row['status'],//0待处理 1同意 2拒绝 3无须处理
                        'userInfo' => $userInfo,
                        'groupInfo' => isset($groupArrMap[$row['group_id']]) ? $groupArrMap[$row['group_id']] : [],
                    ];
                }
            }

            return json(Toolkit::success(['list' => $data, 'count' => $count]));
        }

        Msgbox::where('to', $this->userInfo['id'])
            ->whereNull('read_time')
            ->update(['read_time' => time()]);

        return view();
    }

    /**
     * 同意好友、群申请
     * @return \think\response\Json|void
     * @throws \Exception
     */
    public function agreeFriend()
    {
        $post = $this->request->post();
        $rule = [
            'id' => 'require'
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }
        $msgboxObj = Msgbox::find($post['id']);
        if (empty($msgboxObj)) {
            return json(Toolkit::error('消息不存在'));
        }
        if ($msgboxObj->type === Msgbox::TYPE_MAKE_FRIEND_USER && empty($post['group'])) {
            return json(Toolkit::error('group不能为空'));
        }

        //更新消息状态
        $msgboxObj->status = Msgbox::STATUS_AGREED;
        $msgboxObj->save();

        //加好友
        if (isset($post['group'])) {
            //互相加好友
            $friendModel = new Friend();
            $friendModel->saveAll([
                ['group_id' => $post['group'], 'member_id' => $msgboxObj->from],//加入我的朋友分组
                ['group_id' => $msgboxObj->friend_group_id, 'member_id' => $this->userInfo['id']],//把我加入好友的朋友分组
            ]);

            //发送同意添加好友通知
            $myMemberInfo = Member::fieldRaw("id,account,nickname,avatar,signature,status")
                ->find($this->userInfo['id']);
            Msgbox::create([
                'type' => Msgbox::TYPE_MAKE_FRIEND_SYSTEM,
                'status' => Msgbox::STATUS_IGNORED,
                'to' => $msgboxObj->from,//消息接收者
                'content' => '你和 [' . $myMemberInfo->account . '] 已经是好友了',
                'send_time' => time(),
            ]);
            if (Gateway::isUidOnline($msgboxObj->from)) {
                //通知好友有新消息
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_MSGBOX, [
                    'count' => Msgbox::getUnreadCountByMemberId($msgboxObj->from)
                ]));
                //通知好友更新列表
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_ADD_LIST, [
                    'type' => 'friend', //列表类型，只支持friend和group两种
                    'avatar' => $myMemberInfo->avatar, //好友头像
                    'username' => $myMemberInfo->nickname, //好友昵称
                    'groupid' => $msgboxObj->friend_group_id,//所在的分组id
                    'id' => $myMemberInfo->id, //好友ID
                    'sign' => $myMemberInfo->signature, //好友签名
                    'status' => Member::getStatusText($myMemberInfo->status)
                ]));
            }
        } else {
            //加群
            GroupMember::create([
                'group_id' => $msgboxObj->group_id,
                'member_id' => $msgboxObj->from,
                'add_time' => time()
            ]);

            //发送同意加群通知
            $groupInfo = Group::find($msgboxObj->group_id);
            Msgbox::create([
                'type' => Msgbox::TYPE_JOIN_GROUP_SYSTEM,
                'status' => Msgbox::STATUS_IGNORED,
                'to' => $msgboxObj->from,//消息接收者
                'content' => '你已加入群 [' . $groupInfo['account'] . ']',
                'send_time' => time(),
            ]);
            if (Gateway::isUidOnline($msgboxObj->from)) {
                //通知好友有新消息
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_MSGBOX, [
                    'count' => Msgbox::getUnreadCountByMemberId($msgboxObj->from)
                ]));
                //通知好友更新列表
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_ADD_LIST, [
                    'type' => 'group', //列表类型，只支持friend和group两种
                    'avatar' => $groupInfo->avatar, //群组头像
                    'groupname' => $groupInfo->group_name,//群组名称
                    'id' => $groupInfo->id //群组id
                ]));
            }
        }

        return json(Toolkit::success());
    }


    /**
     * 拒绝好友、群申请
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refuseFriend()
    {
        $post = $this->request->post();
        $rule = [
            'id' => 'require',
            'type' => ['require', 'in:friend,group']
        ];
        try {
            $this->validate($post, $rule);
        } catch (\Exception $e) {
            return json(Toolkit::error($e->getMessage()));
        }
        $msgboxObj = Msgbox::find($post['id']);
        if (empty($msgboxObj)) {
            return json(Toolkit::error('消息不存在'));
        }

        //更新消息状态
        $msgboxObj->status = Msgbox::STATUS_REFUSED;
        $msgboxObj->save();

        if ($post['type'] === 'friend') {
            //发送拒绝添加好友通知
            $myMemberInfo = Member::find($this->userInfo['id']);
            Msgbox::create([
                'type' => Msgbox::TYPE_MAKE_FRIEND_SYSTEM,
                'status' => Msgbox::STATUS_IGNORED,
                'to' => $msgboxObj->from,//消息接收者
                'content' => '[' . $myMemberInfo->account . '] 拒绝了你的好友申请',
                'send_time' => time(),
            ]);
            if (Gateway::isUidOnline($msgboxObj->from)) {
                //通知好友有新消息
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_MSGBOX, [
                    'count' => Msgbox::getUnreadCountByMemberId($msgboxObj->from)
                ]));
            }
        } else {
            //发送同意加群通知
            $groupInfo = Group::find($msgboxObj->group_id);
            Msgbox::create([
                'type' => Msgbox::TYPE_JOIN_GROUP_SYSTEM,
                'status' => Msgbox::STATUS_IGNORED,
                'to' => $msgboxObj->from,//消息接收者
                'content' => '群主拒绝了你的加群 [' . $groupInfo['account'] . '] 申请',
                'send_time' => time(),
            ]);
            if (Gateway::isUidOnline($msgboxObj->from)) {
                //通知好友有新消息
                Gateway::sendToUid($msgboxObj->from, $this->makeMessage(self::EMIT_MSGBOX, [
                    'count' => Msgbox::getUnreadCountByMemberId($msgboxObj->from)
                ]));
            }
        }

        return json(Toolkit::success());
    }

    /**
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chatlog()
    {
        if ($this->request->isAjax()) {
            $get = $this->request->get();
            $rule = [
                'id' => 'require',
                'type' => 'require',
                'page' => 'require',
                'limit' => 'require'
            ];
            try {
                $this->validate($get, $rule);
            } catch (\Exception $e) {
                return json(Toolkit::error($e->getMessage()));
            }

            if ($get['type'] === 'friend') {
                $qb = Db::name('record')->alias('r')
                    ->join(['chat_member' => 'm'], 'm.id=r.sender')
                    ->whereOr([
                        [
                            ['sender', '=', $this->userInfo['id']],
                            ['receiver', '=', $get['id']],
                            ['type', '=', $get['type']]
                        ],
                        [
                            ['receiver', '=', $this->userInfo['id']],
                            ['sender', '=', $get['id']],
                            ['type', '=', $get['type']]
                        ],
                    ])->fieldRaw('m.id,m.account username,m.avatar,r.send_time*1000 as timestamp,r.content');
            } else {
                $qb = Db::name('record')->alias('r')
                    ->join(['chat_member' => 'm'], 'm.id=r.sender')
                    ->where('receiver', $get['id'])
                    ->where('type', $get['type'])
                    ->fieldRaw('m.id,m.account username,m.avatar,r.send_time*1000 as timestamp,r.content');
            }

            $count = $qb->count();
            $list = $qb->order('send_time')
                ->page($get['page'], $get['limit'])
                ->select();

            return json(Toolkit::success(['list' => $list, 'count' => $count]));
        }

        return view();
    }

    /**
     * 通知在线好友我已上线/下线
     * @param bool $online
     * @throws \think\db\exception\DbException
     */
    private function notifyFriendOnlineStatus($online = true)
    {
        $friendGroupIdsArr = FriendGroup::where('member_id', $this->userInfo['id'])->column('id');
        if ($friendGroupIdsArr) {
            $friendMemberIds = Friend::whereIn('group_id', $friendGroupIdsArr)->column('member_id');
            $statusText = $online ? 'online' : 'offline';
            foreach ($friendMemberIds as $memberId) {
                if (Gateway::isUidOnline($memberId)) {
                    Gateway::sendToUid($memberId, $this->makeMessage(self::EMIT_SET_FRIEND_STATUS, [
                        'id' => $this->userInfo['id'],
                        'status' => $statusText
                    ]));
                }
            }
        }
    }

    /**
     * 消息通知
     * @param $emit
     * @param array $data
     * @return false|string
     */
    private function makeMessage($emit, $data = [])
    {
        return json_encode(['emit' => $emit, 'data' => $data]);
    }
}
