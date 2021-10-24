<?php

namespace app\controller;

use app\BaseController;
use app\model\Friend;
use app\model\FriendGroup;
use app\model\Group;
use app\model\GroupMember;
use app\model\Member;
use app\model\Record;
use app\model\Skin;
use Fairy\Toolkit;
use GatewayClient\Gateway;
use think\facade\Db;

class Chat extends BaseController
{
    const SOCKET_NAME = 'ws://127.0.0.1:7272';
    const REGISTER_ADDRESS = '127.0.0.1:1236';

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
            ->value('url', '');

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

        return json(Toolkit::success('绑定成功'));
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
            'status' => $memberInfo->status == Member::STATUS_ONLINE ? 'online' : 'hide',//在线状态 online：在线、hide：隐身
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
                            'status' => $groupFriend['status'] == Member::STATUS_ONLINE && Gateway::isUidOnline($groupFriend['member_id']) ? 'online' : 'offline'//若值为offline代表离线，online或者不填为在线
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
                    Gateway::sendToUid($to['id'], $this->makeMessage('message', [
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
                        Gateway::sendToUid($mine['id'], $this->makeMessage('message', [
                            //来自于系统的聊天面板的消息
                            'system' => true,//系统消息
                            'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                            'type' => $to['type'],//聊天窗口类型
                            'content' => '好友已离线',
                        ]));
                    }
                } else {
                    //好友离线
                    Gateway::sendToUid($mine['id'], $this->makeMessage('message', [
                        //来自于系统的聊天面板的消息
                        'system' => true,//系统消息
                        'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                        'type' => $to['type'],//聊天窗口类型
                        'content' => '好友已离线',
                    ]));
                }
            } else {
                Gateway::sendToUid($to['id'], $this->makeMessage('message', [
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
                Gateway::sendToUid($mine['id'], $this->makeMessage('message', [
                    'system' => true,
                    'id' => $to['id'], //聊天窗口ID，即跟谁聊天
                    'type' => $to['type'],
                    'content' => '群全体禁言',
                ]));
            } else {
                $status = GroupMember::where('member_id', $mine['id'])->value('status');
                //用户禁言
                if ($status) {
                    Gateway::sendToUid($mine['id'], $this->makeMessage('message', [
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
                            Gateway::sendToUid($memberId, $this->makeMessage('message', [
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
                Gateway::sendToUid($memberId, $this->makeMessage('message', $post));
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
        $memberObj->status = $post['value'] === 'online' ? Member::STATUS_ONLINE : Member::STATUS_HIDE;
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
        $skin = Db::name('skin')
            ->where('id', $this->userInfo['id'])
            ->find();
        if (!$skin) {
            Db::name('skin')->insert([
                'member_id' => $this->userInfo['id'],
                'url' => $post['value']
            ]);
        } else {
            Db::name('skin')
                ->where('member_id', $this->userInfo['id'])
                ->update(['url' => $post['value']]);
        }

        return json(Toolkit::success());
    }

    /**
     * 添加好友
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function find()
    {
        $recommendFriends = [];
        $friendIds = Db::table('chat_friend_group')->alias('fg')
            ->join(['chat_friend' => 'f'], 'fg.id=f.group_id')
            ->where('fg.member_id', $this->userInfo['id'])
            ->column('f.member_id');
        if ($friendIds) {
            $notFriendIds = Db::name('member')
                ->where('id', '<>', $this->userInfo['id'])
                ->whereNotIn('id', $friendIds)
                ->column('id');
            if ($notFriendIds) {
                if (count($notFriendIds) > 10) {
                    shuffle($notFriendIds);
                    $notFriendIds = array_rand($notFriendIds, 10);
                }
                $recommendFriends = Db::name('member')
                    ->whereIn('id', $notFriendIds)
                    ->field('id,account,nickname,status,signature,avatar')
                    ->select();
            }
        }

        return view('', ['recommendFriends' => $recommendFriends]);
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
                    ->where('receiver',$get['id'])
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

    public function msgbox()
    {
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
                    Gateway::sendToUid($memberId, $this->makeMessage('friendStatus', [
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
