<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Transaction;
use app\models\User;
use Yii;
use app\models\Wallet;
use app\models\WalletSearch;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\Cors;
/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class WalletController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['my-balance', 'pay'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }

    /**
     * Lists all Wallet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WalletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Wallet model.
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
     * Creates a new Wallet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Wallet();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Wallet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Wallet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionMyBalance($username, $password): array
    {

        $model = new LoginForm();
        $model->username= $username;
        $model->password= $password;
        if ($model->login()) {
            $user = User::findOne(['username'=>$username]);
            $wallet = Wallet::findOne(['author' => $user->id]);
            if (!isset($wallet)) {
                $response['balance'] = 'Please open wallet in the http://online-bank.test !!!';
                return $response;
            }
            $response['balance'] = $wallet->sum;
        } else {
            $response['balance'] = 'Wrong credentials';
        }
        return $response;
    }

    public function actionPay($username, $password, $amount, $to = 1)
    {
        $model = new LoginForm();
        $model->username= $username;
        $model->password= $password;
        if ($model->login()) {
            $user = User::findOne(['username'=>$username]);
            $wallet = Wallet::findOne(['author' => $user->id]);
            if (!isset($wallet)) {
                $response['balance'] = 'Please open wallet in the http://online-bank.test !!!';
                return $response;
            }
            $balance = $wallet->sum;
            $remaining = $balance - $amount;
            if ($remaining<0) {
                $response['balance'] = $wallet->sum;
                $response['payment'] = 'Not enough balance!';
                return $response;
            }
            $wallet->sum = $remaining;
            $wallet->save();

            $receiver = Wallet::findOne(['id' => 4]);
            $receiver->sum = $receiver->sum + $amount;
            $receiver->save();

            $transaction = new Transaction();
            $transaction->sum = $amount;
            $transaction->from = $wallet->id;
            $transaction->to = $receiver->id;
            $transaction->save();

            $response['transaction_id'] = $transaction->id;
            $response['balance'] = $wallet->sum;
            $response['payment'] = $amount;
        } else {
            $response['balance'] = 'Wrong credentials';
        }
        return $response;
    }

    /**
     * Finds the Wallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Wallet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
