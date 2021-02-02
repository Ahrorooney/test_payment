<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\mssql\PDO;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property string $role
 * @property string $created_at
 * @property integer $updated_at
 * @property string $authKey
 * @property string $accessToken
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $new_password;
    public $renew_password;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'username'], 'required'],
            [['password', 'renew_password'], 'required', 'on' => 'create'],
            [['status'], 'integer'],
            [['role', 'created_at','updated_at', 'new_password', 'renew_password'], 'safe'],
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 150],
            [['authKey', 'accessToken'], 'string', 'max' => 255],
            [['username'], 'unique', 'message' => 'Такой логин уже занят'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Актив',
            'role' => 'Роль',
            'username' => 'Пользователь',
            'password' => 'Пароль',
            'authKey' => 'Код авторизации',
            'accessToken' => 'Токен',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Последний визит',
            'new_password' => 'Новый пароль',
            'renew_password' => 'Повторите пароль',
        ];
    }
    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter
     * to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.
     * )
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasOne(Role::className(), ['name' => 'role']);
    }

    public function generatePassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        return Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString();
    }
    public function setRoles()
    {
        $auth = Yii::$app->authManager;
        Yii::$app->db->createCommand("delete from auth_assignment where user_id=:id")
            ->bindValue(":id", $this->id, PDO::PARAM_INT)
            ->execute();
        $roleModel = $auth->getRole($this->role);
        $auth->assign($roleModel, $this->id);
    }
}
