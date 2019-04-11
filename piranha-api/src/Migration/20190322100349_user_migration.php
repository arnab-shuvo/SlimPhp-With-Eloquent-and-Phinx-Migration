<?php


use Phinx\Migration\AbstractMigration;

class UserMigration extends AbstractMigration
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
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $users = $this->table('users');
        $users->addColumn('username', 'string', ['limit' => 24, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('forename', 'string', ['limit' => 24, 'null' => true])
            ->addColumn('surname', 'string', ['limit' => 24, 'null' => true])
            ->addColumn('country', 'string', ['limit' => 24, 'null' => true])
            ->addColumn('gender', 'string', ['limit' => 24, 'null' => true])
            ->addColumn('company', 'string', ['limit' => 24, 'null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'] )
            ->addIndex(['username', 'email'], ['unique' => true])
            ->create();
    }
}
