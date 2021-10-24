<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatGroupNumber extends Migrator
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
        $table = $this->table('chat_group_number', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '群员表']);
        $table->addColumn('group_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '群id'])
            ->addColumn('member_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '用户id'])
            ->addColumn('status', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0正常 1群黑名单'])
            ->addColumn('add_time', 'integer', ['null' => false, 'signed' => false, 'comment' => '加群时间'])
            ->addColumn('type', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0会员 1管理员 2群主'])
            ->addColumn('forbidden_speech_time', 'integer', ['null' => false, 'signed' => false, 'default' => 0, 'comment' => '禁言到某个时间'])
            ->addColumn('nickname', 'string', ['null' => false, 'limit' => 50, 'default' => '', 'comment' => '群员的群昵称'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
