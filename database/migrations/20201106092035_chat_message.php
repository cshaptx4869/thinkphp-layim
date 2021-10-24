<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatMessage extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('chat_message', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '通知表']);
        $table->addColumn('msg_type', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0请求添加用户 1系统消息(加好友) 2请求加群 3系统消息(加群) 4群体会员消息'])
            ->addColumn('send', 'integer', ['null' => false, 'signed' => false, 'comment' => '消息发送者'])
            ->addColumn('receive', 'integer', ['null' => false, 'signed' => false, 'comment' => '消息接收者'])
            ->addColumn('msg_status', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0未读 1同意 2拒绝 3同意且返回消息已读 4拒绝且返回消息已读 5全体消息已读'])
            ->addColumn('remark', 'string', ['null' => false, 'limit' => 128, 'comment' => '附加消息'])
            ->addColumn('send_time', 'integer', ['null' => false, 'signed' => false, 'comment' => '发送消息时间'])
            ->addColumn('read_time', 'integer', ['null' => false, 'signed' => false, 'comment' => '读消息时间'])
            ->addColumn('receive_group', 'integer', ['null' => false, 'comment' => '接收消息的群主'])
            ->addColumn('handle_group', 'integer', ['null' => false, 'comment' => '处理该请求的群主'])
            ->addColumn('my_group', 'integer', ['null' => false, 'comment' => '好友分组'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
