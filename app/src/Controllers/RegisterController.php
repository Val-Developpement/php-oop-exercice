<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class RegisterController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

        $isLoggedIn = isset($_SESSION['user_id']);
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $request->getPayload();
            $username = $data['username'] ?? null;
            $password = $data['password'] ?? null;
            $email = $data['email'] ?? null;
        
            $sql = "SELECT * FROM users WHERE email = :email OR name = :username";
            $stmt = $db->prepare($sql);
            $stmt->execute(['email' => $email, 'username' => $username]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            if (!empty($user)) {
                return new Response(json_encode(['error' => 'User already exists']), 400);
            }
        
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password);";
            $stmt = $db->prepare($sql);
            $stmt->execute(['name' => $username, 'email' => $email, 'password' => $hashedPassword]);
        
            return new Response(json_encode(['message' => 'Registered successfully']), 201);
        }
        
        return new Response(json_encode(['error' => 'Invalid request method']), 405);
    }
}


