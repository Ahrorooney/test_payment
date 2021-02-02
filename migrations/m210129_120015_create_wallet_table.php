<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wallet}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m210129_120015_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wallet}}', [
            'id' => $this->primaryKey(),
            'sum' => $this->bigInteger(),
            'author' => $this->integer(11),
        ]);

        // creates index for column `author`
        $this->createIndex(
            '{{%idx-wallet-author}}',
            '{{%wallet}}',
            'author'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-wallet-author}}',
            '{{%wallet}}',
            'author',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-wallet-author}}',
            '{{%wallet}}'
        );

        // drops index for column `author`
        $this->dropIndex(
            '{{%idx-wallet-author}}',
            '{{%wallet}}'
        );

        $this->dropTable('{{%wallet}}');
    }
}
