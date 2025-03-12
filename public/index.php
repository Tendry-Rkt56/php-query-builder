<?php

use App\App;
use App\Entity\Article;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../vendor/autoload.php';
require_once '../config/Constante.php';


$app = App::getInstance();

$queryBuilder = $app->getQueryBuilder();
$article = $app->getEntity(Article::class);

var_dump($queryBuilder->table('articles')->where('nom', 'LIKE', '%lasopy%')->orWhere('category_id', '=', 2)->get());
var_dump($queryBuilder->table('category')->get());
var_dump($article->findAll());