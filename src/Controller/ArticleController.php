<?php

namespace App\Controller;

use App\Entity\Article;

class ArticleController extends Controller
{

     public function index()
     {
          $articles = $this->getEntity(Article::class)->findAll();
          return $this->render('articles/index.html.php', [
               'articles' => $articles,
          ]);
     }

     // public function create()
     // {
     //      return $this->render('articles/create.html.php');
     // }

}