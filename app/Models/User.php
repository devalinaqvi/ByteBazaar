<?php

namespace App\Models;

class User
{
    public int $id;

    public string $name;

    public string $email;
    public string $password;
    public int $is_admin = 0;

    public mixed $created_at;

    public function __construct(array $data)
    {
        $this->id       = $data['id'] ?? 0;
        $this->name     = $data['name'];
        $this->email    = $data['email'];
        $this->password = $data['password'];
        $this->is_admin = $data['is_admin'] ?? 0;
        $this->created_at = $data['created_at'] ?? null;
    }
}
