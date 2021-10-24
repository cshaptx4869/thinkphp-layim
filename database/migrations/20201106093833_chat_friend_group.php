<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatFriendGroup extends Migrator
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
        $table = $this->table('chat_friend_group', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '会员好友分组表']);
        $table->addColumn('group_name', 'string', ['null' => false, 'limit' => 60, 'comment' => '分组名称'])
            ->addColumn('member_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '会员id'])
            ->addColumn('weight', 'integer', ['null' => false, 'default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '好友分组排序 越小越前'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
