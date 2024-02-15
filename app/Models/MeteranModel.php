<?php

namespace App\Models;

use CodeIgniter\Model;

class MeteranModel extends Model
{
    protected $table = 'meteran';
    protected $table_pelanggan = 'pelanggan';
    protected $table_penggunaan = 'penggunaan';
    protected $allowedFields = ['id_pelanggan', 'nomor_meteran'];

    public function getJoinedData()
    {
        $builder = $this->db->table($this->table);
        $builder->join($this->table_pelanggan, 'pelanggan.id = meteran.id_pelanggan');
        $builder->select('meteran.*, pelanggan.name');

        return $builder->get()->getResult();
    }

    public function getPengguna($id) {
        $builder = $this->db->table($this->table);
        $builder->join($this->table_pelanggan, 'pelanggan.id = meteran.id_pelanggan');
        $builder->select('meteran.*, pelanggan.name');

        if ($id) {
            $builder->where('meteran.id', $id);
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    public function get_pelanggan_not_in_meteran_data()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id_pelanggan');
        $result = $builder->get()->getResult();

        $ids = array_column($result, 'id_pelanggan');

        $builder2 = $this->db->table($this->table_pelanggan);
        if (!empty($ids)) {
            $builder2->whereNotIn('id', $ids);
        }

        return $builder2->get()->getResult();
    }

    public function getDataWhereMonth($data)
    {
        $builder = $this->db->table($this->table);
        $builder->join($this->table_penggunaan, 'meteran.id = penggunaan.id_meteran');
        $builder->where("DATE_FORMAT(penggunaan.bulan,'%Y-%m') = ", $data);
        $result = $builder->get()->getResult();

        $id = array_column($result, 'id_meteran');

        $builder2 = $this->db->table($this->table);
        if (!empty($id)) {
            $builder2->whereNotIn('id', $id);
        }

        return $builder2->get()->getResult();
    }
}
