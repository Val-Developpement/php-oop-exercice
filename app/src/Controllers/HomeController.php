<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class HomeController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

        $getPostsCount = $db->query("SELECT COUNT(*) FROM posts")->fetchAll(\PDO::FETCH_ASSOC);
        $isLoggedIn = isset($_SESSION['user_id']);
        $getPage = $_GET['page'] ?? 1;
        $getLimit = $_GET['limit'] ?? 10;
        $postsCount = $getPostsCount[0]['COUNT(*)'];
        $postsPerPage = $getLimit;
        $pagesCount = ceil($postsCount / $postsPerPage);

       
        
        $posts = $db->query("SELECT posts.id, posts.title, posts.created_at, users.name, users.id as user_id
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC
        LIMIT 10
        OFFSET 0")->fetchAll(\PDO::FETCH_ASSOC);
        
        
        $currentPage = $getPage;
        $postsPerPage = $getLimit;
        $offset = ($currentPage - 1) * $postsPerPage;
        
            
        $responseData = [
            'isLoggedIn' => $isLoggedIn,
            'currentPage' => $currentPage,
            'postsPerPage' => $postsPerPage,
            'pagesCount' => $pagesCount,
            'posts' => $posts
        ];

        return new Response(json_encode($responseData), 200, ['Content-Type' => 'application/json']);
       
    }
}
