<?php

namespace App\Models;

use CodeIgniter\Model;

class InfoPesertaModel extends Model
{
    protected $table      = 'info_peserta';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['instansi', 'foto', 'startDate', 'endDate', 'pengantar', 'proposal', 'userId', 'ket', 'statuspgj'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function CountAllPesertaAktif()
    {
        $query = $this->db->table('info_peserta')
            ->select('
                        user.*,
                        info_peserta.*
                        info_peserta.status as status_pengajuan,
                        user.status as status_user,
                        user.role as role
                    ')
            ->join('user', 'info_peserta.userId = user.id')
            ->where('user.role', 3)
            ->where('user.status', 2)
            ->where('info_peserta.endDate >', date('Y-m-d'))
            ->countAllResults();
        return $query;
    }
}
