<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

class ChatRecord extends Migrator
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
        $table = $this->table('chat_record', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '聊天记录表']);
        $table->addColumn('send', 'integer', ['null' => false, 'signed' => false, 'comment' => '发送者'])
            ->addColumn('receive', 'integer', ['null' => false, 'signed' => false, 'comment' => '接收者'])
            ->addColumn('content', 'string', ['null' => false, 'limit' => 1000, 'comment' => '发送内容'])
            ->addColumn('send_time', 'integer', ['null' => false, 'signed' => false, 'comment' => '发送时间'])
            ->addColumn('type', 'enum', ['null' => false, 'values' => ['friend', 'group'], 'default' => 'friend', 'comment' => '聊天类型'])
            ->addColumn('is_read', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0未读 1已读'])
            ->addTimestamps()
            ->addSoftDelete()
            ->addIndex('send')
            ->addIndex('receive')
            ->create();
    }
}
