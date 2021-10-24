<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatGroup extends Migrator
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
        $table = $this->table('chat_group', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '聊天群表']);
        $table->addColumn('account', 'string', ['null' => false, 'limit' => 20, 'comment' => '群号'])
            ->addColumn('group_name', 'string', ['null' => false, 'limit' => 60, 'comment' => '群名称'])
            ->addColumn('avatar', 'string', ['null' => false, 'limit' => 128, 'default' => '/static/images/pkq.png', 'comment' => '群头像'])
            ->addColumn('belong', 'integer', ['null' => false, 'signed' => false, 'comment' => '群主'])
            ->addColumn('number', 'integer', ['null' => false, 'signed' => false, 'default' => 0, 'comment' => '人数'])
            ->addColumn('desc', 'string', ['null' => false, 'limit' => 200, 'default' => '', 'comment' => '描述'])
            ->addColumn('approval', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '0无需验证 1需要验证'])
            ->addColumn('group_status', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0正常 1全体禁言'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
