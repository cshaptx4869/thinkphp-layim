<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatFriend extends Migrator
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
        $table = $this->table('chat_friend', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '会员分组下的好友列表']);
        $table->addColumn('group_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '分组id'])
            ->addColumn('member_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '好友id'])
            ->addColumn('nickname', 'string', ['null' => false, 'limit' => 30, 'default' => '', 'comment' => '好友昵称'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
