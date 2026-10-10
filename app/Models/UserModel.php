<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'full_name', 'password', 'avatar', 'created_at'];
    protected $useTimestamps = false;

    public function hasConfiguredAccount(): bool
    {
        return $this->countAllResults() > 0;
    }

    /** @return array<string, mixed>|null */
    public function getById(int $id): ?array
    {
        $row = $this->find($id);
        return is_array($row) ? $row : null;
    }

    /** @return array<string, mixed>|null */
    public function getByUsername(string $username): ?array
    {
        $row = $this->where('username', $username)->first();
        return is_array($row) ? $row : null;
    }

    /** @return array<string, mixed>|null */
    public function currentPublicUser(): ?array
    {
        $id = session()->get('userId');
        if (! is_numeric($id)) return null;
        $row = $this->select('id, username, full_name, avatar, created_at')->find((int) $id);
        return is_array($row) ? $row : null;
    }

    public function hasSales(int $id): bool
    {
        return $this->db->table('sales')->where('sold_by', $id)->countAllResults() > 0;
    }
}
