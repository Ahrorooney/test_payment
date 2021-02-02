<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%wallet}}`
 * - `{{%wallet}}`
 */
class m210129_121557_create_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'sum' => $this->bigInteger()->notNull(),
            'from' => $this->integer(),
            'to' => $this->integer(),
            'created_at' => $this->integer(11),
        ]);

        // creates index for column `from`
        $this->createIndex(
            '{{%idx-transaction-from}}',
            '{{%transaction}}',
            'from'
        );

        // add foreign key for table `{{%wallet}}`
        $this->addForeignKey(
            '{{%fk-transaction-from}}',
            '{{%transaction}}',
            'from',
            '{{%wallet}}',
            'id',
            'CASCADE'
        );

        // creates index for column `to`
        $this->createIndex(
            '{{%idx-transaction-to}}',
            '{{%transaction}}',
            'to'
        );

        // add foreign key for table `{{%wallet}}`
        $this->addForeignKey(
            '{{%fk-transaction-to}}',
            '{{%transaction}}',
            'to',
            '{{%wallet}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%wallet}}`
        $this->dropForeignKey(
            '{{%fk-transaction-from}}',
            '{{%transaction}}'
        );

        // drops index for column `from`
        $this->dropIndex(
            '{{%idx-transaction-from}}',
            '{{%transaction}}'
        );

        // drops foreign key for table `{{%wallet}}`
        $this->dropForeignKey(
            '{{%fk-transaction-to}}',
            '{{%transaction}}'
        );

        // drops index for column `to`
        $this->dropIndex(
            '{{%idx-transaction-to}}',
            '{{%transaction}}'
        );

        $this->dropTable('{{%transaction}}');
    }
}
