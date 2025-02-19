<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class BlogNewController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

        $isLoggedIn = isset($_SESSION['user_id']);
       
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $request->getPayload();

            $title = $data['title'] ?? null;
            $content = $data['content'] ?? null;
            $userId = $_SESSION['user_id'];
            
            if (!$isLoggedIn) {
                return new Response(json_encode(['error' => 'You must be logged in to create a post']), 401);
            }

            if (!$title || !$content) {
                return new Response(json_encode(['error' => 'Title and content are required']), 400);
            }
            
            
            $stmt = $db->prepare("INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)");
            $stmt->execute(['title' => $title, 'content' => $content, 'user_id' => $userId]);
            $post = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            return new Response(json_encode(['message' => 'Post added successfully']), 201, ['Content-Type' => 'application/json']);
            
        }
    }
}
