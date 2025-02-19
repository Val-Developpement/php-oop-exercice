<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class UsersController extends AbstractController
{

    private function getPage(): int {
        return $_GET['page'] ?? 1;
    }
    
    private function getLimit(): int {
        return $_GET['limit'] ?? 10;
    }

    private function getPagination(): array {
        $postsCount = getPostsCount();
        $postsPerPage = $this->getLimit();
        $pagesCount = ceil($postsCount / $postsPerPage);
    
        return [
            'pagesCount' => $pagesCount,
            'currentPage' => $this->getPage(),
        ];
    }

    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

       
       


        $currentPage = $this->getPage();
            $postsPerPage = $this->getLimit();
            $offset = ($currentPage - 1) * $postsPerPage;
            
            $sql = "SELECT posts.id, posts.title, posts.created_at
            FROM posts 
            INNER JOIN users ON posts.user_id = users.id
            WHERE posts.user_id = :id
            ORDER BY posts.created_at DESC
            LIMIT 10
            OFFSET $offset;
            ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $_GET['id']]);
        $post = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $count = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $user = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
       
        
      
        
        
        
        
        
        
        
        
        
        
        
       
            
        
        return new Response(json_encode([
            'post' => $post,
            'count' => $count,
            'user' => $user
        ]), 200, ['Content-Type' => 'application/json']);
    }
}
