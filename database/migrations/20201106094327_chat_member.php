<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ChatMember extends Migrator
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
        $table = $this->table('chat_member', ['collation' => 'utf8mb4_general_ci', 'signed' => false, 'comment' => '会员表']);
        $table->addColumn('account', 'string', ['null' => false, 'limit' => 30, 'comment' => '账号'])
            ->addColumn('password', 'char', ['null' => false, 'limit' => 32, 'comment' => '发送者'])
            ->addColumn('salt', 'string', ['null' => false, 'limit' => 20, 'comment' => '密钥'])
            ->addColumn('nickname', 'string', ['null' => false, 'limit' => 50, 'default' => '匿名', 'comment' => '昵称'])
            ->addColumn('birthday', 'integer', ['null' => false, 'signed' => false, 'default' => 0, 'comment' => '生日'])
            ->addColumn('sex', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '性别 0保密 1男 2女'])
            ->addColumn('avatar', 'string', ['null' => false, 'limit' => 100, 'default' => '/static/images/pkq.png', 'comment' => '头像'])
            ->addColumn('email', 'string', ['null' => false, 'limit' => 60, 'default' => '', 'comment' => '邮箱'])
            ->addColumn('mobile', 'string', ['null' => false, 'limit' => 11, 'default' => '', 'comment' => '手机'])
            ->addColumn('signature', 'string', ['null' => false, 'limit' => 100, 'default' => '', 'comment' => '签名'])
            ->addColumn('status', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '在线状态 0在线 1离线'])
            ->addColumn('login_time', 'integer', ['null' => false, 'signed' => false, 'default' => 0, 'comment' => '上次登录时间'])
            ->addTimestamps()
            ->addSoftDelete()
            ->create();
    }
}
