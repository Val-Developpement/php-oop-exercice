<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class ProfileController extends AbstractController
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
            $name = $data['name'] ?? null;
            $password = $data['password'] ?? null;
            $email = $data['email'] ?? null;

          
        
            $userId = $_SESSION['user_id'];

            $sql = "UPDATE users SET name = :name, email = :email";
            $params = ['name' => $name, 'email' => $email, 'id' => $userId];

            if($password) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            
            $sql .= " WHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return new Response(json_encode(['message' => 'Profile updated successfully']), 200);
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT * FROM posts WHERE user_id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);


       

        


        return new Response(json_encode([
            'user' => $user,
            'posts' => $posts,
        ]), 200, ['Content-Type' => 'application/json']);
    }
}
