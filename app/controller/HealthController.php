<?php

namespace App\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class HealthController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * GET /health
     *
     * Application umumiy holatini tekshiradi.
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'status' => 'UP',
            'application' => Yii::$app->name,
        ];
    }

    /**
     * GET /health/db
     *
     * PostgreSQL connectionni tekshiradi.
     */
    public function actionDb()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            Yii::$app->db->open();

            Yii::$app->db->createCommand('SELECT 1')->queryScalar();

            return [
                'status' => 'UP',
                'database' => 'PostgreSQL',
            ];
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 503;

            return [
                'status' => 'DOWN',
                'database' => 'PostgreSQL',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * GET /health/info
     *
     * Application haqida ma'lumot.
     */
    public function actionInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'application' => Yii::$app->name,
            'environment' => YII_ENV,
            'debug' => YII_DEBUG,
            'php_version' => PHP_VERSION,
            'yii_version' => Yii::getVersion(),
        ];
    }
}