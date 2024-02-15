<?php

namespace App\Models;

use CodeIgniter\Model;

class SaldoModel extends Model
{
    protected $table = 'saldo';
    protected $table_pelanggan = 'pelanggan';
    protected $allowedFields = ['id_pelanggan', 'saldo'];

    public function getJoined()
    {
        $result = $this->db->table($this->table);
        $result->join($this->table_pelanggan, 'saldo.id_pelanggan = pelanggan.id');
        $result->select('saldo.*, pelanggan.name');

        return $result->get()->getResult();
    }
}
