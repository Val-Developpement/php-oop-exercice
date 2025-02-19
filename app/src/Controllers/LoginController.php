<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class LoginController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        $db = (new ConnectDatabase())->execute();

        if ($db === false) {
            throw new \Exception("Database connection failed");
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $request->getPayload();
            

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            if (!$email || !$password) {
            return new Response(json_encode(['error' => 'Email and password are required']), 400);
            }

            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
            return new Response(json_encode(['error' => 'Invalid email or password']), 401);
            }

            $_SESSION['user_id'] = $user['id'];
            return new Response(json_encode(['message' => 'Login successful']), 200);
        }
    }
}
