<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class BlogIndexController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

        $isLoggedIn = isset($_SESSION['user_id']);
       
        $stmt = $db->prepare("SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $post = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id = :id");
        $stmt->execute(['id' => $post['user_id']]);
        $author = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $stmt = $db->prepare("SELECT comments.*, users.name as user_name, users.id as user_id FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $post['id']]);
        $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
       
        
      
        
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comment = $_POST['comment'];

            if ($isLoggedIn === false) {
            return new Response(json_encode(['error' => 'User not logged in']), 401, ['Content-Type' => 'application/json']);
            }

            $commentData = [
            'content' => $comment,
            'post_id' => $post['id'],
            'user_id' => $_SESSION['user_id'],
            ];

            $stmt = $db->prepare("INSERT INTO comments (content, post_id, user_id) VALUES (:content, :post_id, :user_id)");
            $stmt->execute($commentData);

            return new Response(json_encode(['message' => 'Comment added successfully']), 201, ['Content-Type' => 'application/json']);
        }
            
        
        return new Response(json_encode([
            'post' => $post,
            'author' => $author,
            'comments' => $comments
        ]), 200, ['Content-Type' => 'application/json']);
    }
}
