<?php
/** @author Akhror Gaibnazarov <akhrorgaibnazarov@gmail.com> */

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'profile', 'view', 'create', 'update'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProfile()
    {
        $model = $this->findModel(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->new_password) && $model->new_password == $model->renew_password) {
                $model->password = $model->generatePassword($model->new_password);
            }
            if ($model->save()) {
                // Logs::create("Пользователи", "Изменил пользователя: " . $model->username);
                //                Yii::$app->session->setFlash('success', 'Запись сохранена!');
                return $this->redirect(['/profile']);
            }
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';
        $model->delete_status = 0;
        if ($model->load(Yii::$app->request->post())) {
            $model->authKey = $model->generateAuthKey();
            $model->accessToken = $model->generateAccessToken();
            $model->reg_date = date("Y-m-d H:i:s");
            $model->password = $model->generatePassword($model->password);

            if ($model->save()) {
                $model->setRoles();
                // Logs::create("Пользователи", "Добавил новый пользователь: " . $model->username);
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
                return $this->redirect(['index']);
            }
            // echo '<pre>' . var_export($model, true) . '</pre>';die();
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->new_password) && $model->new_password == $model->renew_password) {
                $model->password = $model->generatePassword($model->new_password);
            }
            if ($model->save()) {
                $model->setRoles();
                // Logs::create("Пользователи", "Изменил пользователя: " . $model->username);
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
            // 'active_disable' => $active_disable,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }
}
