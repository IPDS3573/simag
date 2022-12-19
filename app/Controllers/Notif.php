<?php

namespace App\Controllers;

use App\Models\AbsenModel;
use App\Models\AktivitasModel;
use App\Models\UserModel;
use App\Models\InfoPesertaModel;
use CodeIgniter\API\ResponseTrait;

class Notif extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        echo view('templates/header');
        echo view('templates/sidebarnotif');
        echo view('templates/topbar');
        echo view('peserta/notif.php');
    }

    public function ajukan($id = null)
    {
        if (isset($_POST['submit'])) {
            // validasi pendaftaran    
            if (!$this->validate([

                'instansi'    => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan asal instansi/sekolah Anda']
                ],
                'startDate' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan tanggal mulai PKL']
                ],
                'endDate' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan tanggal selesai PKL']
                ],
                'pengantar' => [
                    'uploaded[pengantar]',
                    'mime_in[pengantar,application/pdf,application/zip,application/msword,application/x-tar]',
                    'max_size[pengantar,10000]',
                    'errors' => [
                        'uploaded' => 'Anda belum memilih file',
                        'mime_in' => 'Format file harus berupa pdf',
                        'max_size' => 'Ukuran maksimal file 10MB'
                    ]
                ],
                'proposal' => [
                    'uploaded[proposal]',
                    'mime_in[proposal,application/pdf,application/zip,application/msword,application/x-tar]',
                    'max_size[proposal,10000]',
                    'errors' => [
                        'uploaded' => 'Anda belum memilih file',
                        'mime_in' => 'Format file harus berupa pdf',
                        'max_size' => 'Ukuran maksimal file 10MB'
                    ]
                ],
            ])) {
                session()->setFlashdata('failed', 'Maaf! Terdapat kesalahan dalam pengisian data.');
                return redirect()->to(base_url('Notif/ajukan'))->withInput();
            }

            $userModel = new UserModel();

            // $id = $userModel->getInsertID();
            $user = $userModel->where('nama', session()->nama)->first();
            $id = $user['id'];

            $pengantar = $this->request->getFile('pengantar');
            $proposal = $this->request->getFile('proposal');
            $nama_pengantar = $pengantar->getName();
            $nama_proposal = $proposal->getName();
            //Menyimpan file yang diupload kedalam file assets
            $pengantar->move('./assets/dokumen pengantar/' . $id, $nama_pengantar);
            $proposal->move('./assets/dokumen proposal/' . $id, $nama_proposal);
            $info_peserta = [
                'instansi' => $this->request->getPost('instansi'),
                'startDate' => $this->request->getPost('startDate'),
                'endDate' => $this->request->getPost('endDate'),
                'userId' => $id,
                'pengantar' => '/assets/dokumen pengantar/' . $id . '/' . $nama_pengantar,
                'proposal' => '/assets/dokumen proposal/' . $id . '/' . $nama_proposal
            ];

            $infoPesertaModel = new InfoPesertaModel();
            $infoPesertaModel->save($info_peserta);
            session()->setFlashdata('success', 'Sukses! Anda berhasil melakukan pendaftaran.');
            return redirect()->to(base_url('dashboard/notif'));
        }
        echo view('templates/header');
        echo view('spadmin/pengajuan');
    }



    public function datapengajuan($id)
    {
        $user = session()->userId;
        $userModel = new UserModel();
        $infoPesertaModel = new InfoPesertaModel();
        $aktif = $infoPesertaModel->select('info_peserta.id as acid, info_peserta.*,user.*')
            ->join('user', 'info_peserta.userId=user.id')
            ->where('userId', $id)
            ->get()->getResultArray();

        $data = [
            'aktif' => $aktif
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebarNotif');
        echo view('templates/topbar');
        echo view('spadmin/datapengajuan.php');
        echo view('templates/footer');
    }

    public function detail()
    {
        $id = session()->userId;
        $userModel = new UserModel();
        $user = $userModel->join('info_peserta', 'user.id=info_peserta.userId')->where('info_peserta.userId', $id)->first();
        $data = [
            'user' => $user
        ];
        $infoModel = new InfoPesertaModel();
        $info = $infoModel->where('userId', $id)->first();
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'foto' => [
                    'uploaded[foto]',
                    'mime_in[foto,image/jpg,image/jpeg,image/png]',
                    'max_size[foto,1024]',
                    'errors' => [
                        'uploaded' => 'Anda belum memilih foto',
                        'mime_in' => 'Foto harus berformat jpg/jpeg/png',
                        'max_size' => 'Ukuran maksimal foto 1MB'
                    ]
                ],
            ])) {
                return redirect()->to(base_url('Peserta/Profil'))->withInput();
            }
            $avatar = $this->request->getFile('foto');
            $randomname = $avatar->getRandomName();
            $avatar->move(ROOTPATH . 'public/assets/fotoprofil/' . $id, $randomname);
            $profil = [
                'foto' => '/assets/fotoprofil/' . $id . '/' . $randomname
            ];
            $infoModel->update($info['id'], $profil);

            return redirect()->to(base_url('Peserta/Profil'));
        }
        echo view('templates/header', $data);
        echo view('templates/sidebarnotif');
        echo view('templates/topbar');
        echo view('peserta/profil');
        echo view('templates/footer');
    }
}
