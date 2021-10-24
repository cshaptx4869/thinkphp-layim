<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatSkin extends Migrator
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
        $table = $this->table('chat_skin', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '皮肤表']);
        $table->addColumn('member_id', 'integer', ['null' => false, 'signed' => false, 'comment' => '会员id'])
            ->addColumn('url', 'string', ['null' => false, 'limit' => 128, 'comment' => '皮肤地址'])
            ->addColumn('is_user_upload', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '0默认 1用户自定义'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
