<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $sum
 * @property int|null $from
 * @property int|null $to
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Wallet $from0
 * @property Wallet $to0
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sum'], 'required'],
            [['sum', 'from', 'to', 'created_at', 'updated_at'], 'integer'],
            [['from'], 'exist', 'skipOnError' => true, 'targetClass' => Wallet::className(), 'targetAttribute' => ['from' => 'id']],
            [['to'], 'exist', 'skipOnError' => true, 'targetClass' => Wallet::className(), 'targetAttribute' => ['to' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sum' => 'Sum',
            'from' => 'From',
            'to' => 'To',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * Gets query for [[From0]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\WalletQuery
     */
    public function getFrom0()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'from']);
    }

    /**
     * Gets query for [[To0]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\WalletQuery
     */
    public function getTo0()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'to']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\TransactionQuery(get_called_class());
    }
}
