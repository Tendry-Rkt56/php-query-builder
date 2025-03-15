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
var_dump($queryBuilder->table('articles')->where('id', '=', 84)->delete());
// var_dump($queryBuilder->table('articles')->insert(['nom' => 'Lasopy Chinoise', 'prix' => 5000, 'category_id' => 5]));
var_dump($article->find(90));