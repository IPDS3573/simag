<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AbsenModel;
use App\Models\AktivitasModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\InfoPesertaModel;
use App\Models\NotifModel;
use App\Models\NilaiModel;
use App\Models\Divisi;
use Dompdf\Dompdf;

class Admin extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $aktivitasModel = new AktivitasModel();
        $userModel = new UserModel();
        $infopesertaModel = new InfoPesertaModel();
        $pesertaAktif = $userModel->where('role', 3)
            ->where('user.status', 2)
            ->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))
            ->countAllResults();

        $pesertaDeaktif = $userModel->where('role', 3)
            ->where('user.status', 2)
            ->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate<=', date('Y-m-d'))
            ->countAllResults();


        $pendaftar = $userModel->where('role', 3)->where('user.status', 1)->countAllResults();
        $pembimbing = $userModel->where('role', 2)->countAllResults();
        $admin = $userModel->where('role', 1)->countAllResults();
        $belumsetuju = $aktivitasModel->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 0)->countAllResults();
        $setuju = $aktivitasModel->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 1)->get()->getResultArray();

        $data = [
            'pesertaAktif' => $pesertaAktif,
            'pesertaDeaktif' => $pesertaDeaktif,
            'pembimbing' => $pembimbing,
            'pendaftar' => $pendaftar,
            'belumsetuju' => $belumsetuju,
            'admin' => $admin
        ];


        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/admin', $data);
        echo view('templates/footer');
    }

    // Data pembimbing
    public function dataPembimbing()
    {
        $userModel = new UserModel();
        $detail = $userModel->where('role', 1)->orWhere('role', 2)->get()->getResultArray();

        $data = [
            'detail' => $detail
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/datapembimbing.php');
        echo view('templates/footer');
    }

    public function tambahPembimbing()
    {
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'nama' => [
                    'rules' => 'required', 'errors' => ['required' => 'nama harus diisi']
                ],
                'JK' => [
                    'rules' => 'required', 'errors' => ['required' => 'Jenis Kelamin harus diisi']
                ],
                'role' => [
                    'rules' => 'required', 'errors' => ['required' => 'Peran harus diisi']
                ],
                'tglLahir' => [
                    'rules' => 'required', 'errors' => ['required' => 'tanggal lahir harus diisi']
                ],
                'email'    => [
                    'required', 'valid_email', 'errors' => ['required' => 'email harus diisi', 'valid_email' => 'email tidak valid']
                ]
            ])) {
                session()->setFlashdata('error', $this->validator->listErrors());
                return redirect()->back()->withInput();
            }

            $data = [
                'nama' => $this->request->getPost('nama'),
                'jenisKelamin' => $this->request->getPost('JK'),
                'tglLahir' => $this->request->getPost('tglLahir'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'status' => 1
            ];
            $userModel = new UserModel();
            $userModel->save($data);
            session()->setFlashdata('success', 'Sukses! Anda berhasil menambahkan data.');
            return redirect()->to(base_url('dashboard/admin/pembimbing'));
        }
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/tambahPembimbing');
        echo view('templates/footer');
    }

    public function editPembimbing($id)
    {
        $userModel = new UserModel();
        $data = $userModel->find($id);
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'nama' => [
                    'rules' => 'required', 'errors' => ['required' => 'nama harus diisi']
                ],
                'JK' => [
                    'rules' => 'required', 'errors' => ['required' => 'Jenis Kelamin harus diisi']
                ],
                'tglLahir' => [
                    'rules' => 'required', 'errors' => ['required' => 'tanggal lahir harus diisi']
                ],
                'email'    => [
                    'required', 'valid_email', 'errors' => ['required' => 'email harus diisi', 'valid_email' => 'email tidak valid']
                ]
            ])) {
                session()->setFlashdata('failed', 'Maaf! Terdapat kesalahan dalam pengisian data.');
                return redirect()->to(base_url('dashboard/admin/pembimbing/edit/' . $id))->withInput();
            }
            $data = [
                'nama' => $this->request->getPost('nama'),
                'jenisKelamin' => $this->request->getPost('JK'),
                'tglLahir' => $this->request->getPost('tglLahir'),
                'email' => $this->request->getPost('email'),
            ];
            $userModel->update($id, $data);
            session()->setFlashdata('success', 'Sukses! Anda berhasil mengubah data.');
            return redirect()->to(base_url('dashboard/admin/pembimbing'));
        }

        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/editpembimbing', $data);
        echo view('templates/footer');
    }

    public function hapusPembimbing($id)
    {
        $userModel = new UserModel();
        $userModel->where('id', $id)->delete();
        session()->setFlashData('success', 'user berhasil dihapus!');
        return $this->response->redirect(site_url('dashboard/admin/pembimbing'));
    }

    // Data peserta
    public function dataPeserta()
    {
        $userModel = new UserModel();
        $user = $userModel->where('role', 3)->get()->getResultArray();
        $aktif = $userModel->getUserDivisi()->getResultArray();
        $deaktif = $userModel->where('role', 3)->where('user.status', 3)->get()->getResultArray();
        $pendaftar = $userModel->where('role', 3)->where('user.status', 1)->get()->getResultArray();

        $data = [
            'user' => $user,
            'pendaftar' => $pendaftar,
            'aktif' => $aktif,
            'deaktif' => $deaktif,
        ];
        //dd($aktif);
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/datapeserta.php', $data);
        echo view('templates/footer');
    }

    // public function terimaPeserta($id)
    // {
    //     $userModel = new UserModel();
    //     $infoPesertaModel = new InfoPesertaModel();

    //     $info = $infoPesertaModel->join('user', 'info_peserta.userId=user.id')->where('info_peserta.status', diterima)->get()->getResultArray();
    //     $data = [
    //         'info' => $info
    //     ];
    //     $userModel->update($id, $data);
    //     session()->setFlashData('success', 'Peserta telah diterima!');
    //     return redirect()->to(base_url('dashboard/admin/peserta'));
    // }

    public function hapusPeserta($id)
    {
        $userModel = new UserModel();
        $userModel->where('id', $id)->delete();
        session()->setFlashData('success', 'Peserta berhasil dihapus!');
        return redirect()->to(base_url('dashboard/admin/peserta'));
    }

    public function detailPeserta($id)
    {
        $userModel = new UserModel();
        $user = $userModel->join('info_peserta', 'user.id=info_peserta.userId')->where('info_peserta.userId', $id)->first();
        $data = [
            'user' => $user
        ];
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/detailpeserta', $data);
        echo view('templates/footer');
    }

    // Data Absensi
    public function dataAbsen()
    {
        $absenModel = new AbsenModel();
        $absen = $absenModel->select('absen.id as acid, absen.*,user.*')->join('user', 'absen.user_id=user.id')->get()->getResultArray();

        $data = [
            'absen' => $absen
        ];

        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/dataabsen', $data);
        echo view('templates/footer');
    }

    public function detailAbsen($id)
    {
        $absenModel = new AbsenModel();
        $absen = $absenModel->where('id', $id)->first();
        $data = [
            'absen' => $absen
        ];
        echo view('templates/header', $data);
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/detailAbsen.php');
        echo view('templates/footer');
    }

    public function getAbsenKoordinat($id)
    {
        $absenModel = new AbsenModel();
        $respond = $absenModel->where('id', $id)->get()->getRowArray();
        if ($respond) {
            return $this->respond($respond);
        }
        return $this->respond([]);
    }
    // Data Laporan Aktivitas Harian
    public function dataAktivitas()
    {
        $aktivitasModel = new AktivitasModel();
        $setuju = $aktivitasModel->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 1)->get()->getResultArray();
        $belumsetuju = $aktivitasModel->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 0)->get()->getResultArray();

        $data = [
            'setuju' => $setuju,
            'belumsetuju' => $belumsetuju
        ];

        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/dataaktivitas.php', $data);
        echo view('templates/footer');
    }
    // Buka file proposal
    public function bukaProposal($id)
    {
        $infoPeserta = new InfoPesertaModel();
        $file = $infoPeserta->where('id', $id)->get()->getRow();
        $data = [
            'link' => $file->proposal
        ];
        echo view('templates/header', $data);
        echo view('templates/topbar');
        echo view('templates/file.php');
        echo view('templates/footer');
    }

    // Buka file pengantar
    public function bukaPengantar($id)
    {
        $infoPeserta = new InfoPesertaModel();
        $file = $infoPeserta->where('id', $id)->get()->getRow();
        $data = [
            'link' => $file->pengantar
        ];
        echo view('templates/header', $data);
        echo view('templates/topbar');
        echo view('templates/file.php');
        echo view('templates/footer');
    }

    public function pesan($id)
    {
        $infopesertaModel = new InfoPesertaModel();
        $userModel = new UserModel();
        $data = [
            'content' => $infopesertaModel->where('id', $id)->first(),
        ];
        $id_infopeserta = $infopesertaModel->where('id', $id)->first();
        $user = $userModel->where('id', $id_infopeserta['userId'])->first();
        //dd($id);
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'ket' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan detail kegiatan']
                ],
            ])) {
                session()->setFlashdata('failed', 'Maaf! Terdapat kesalahan dalam pengisian data.');
                return redirect()->to(base_url('dashboard/admin/pengajuan/pesan/' . $id))->withInput();
            }
            $data = [
                'id' => $id,
                'ket' => $this->request->getPost('ket'),
                'statuspgj' => $this->request->getPost('statuspgj')
            ];

            if ($data['statuspgj'] === 'diterima') {
                if ($infopesertaModel->update($id, $data)) {
                    $data = [
                        'status' => '2',
                    ];
                    $userModel->update($user['id'], $data);
                    session()->setFlashdata('success', 'Sukses! update user.');
                    return redirect()->to(base_url('dashboard/admin/pengajuan/'));
                } else {
                    session()->setFlashdata('error', 'gagal! update user.');
                    return redirect()->to(base_url('dashboard/admin/pengajuan/'));
                }
            } else {
                $infopesertaModel->update($id, $data);
                session()->setFlashdata('success', 'Sukses! Anda berhasil update pengajuan');
                return redirect()->to(base_url('dashboard/admin/pengajuan/'));
            }
        }

        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/pesan', $data);
        echo view('templates/footer');
    }

    public function dataPengajuan()
    {
        $userModel = new UserModel();
        $infoPesertaModel = new InfoPesertaModel();
        $aktif = $infoPesertaModel->select('info_peserta.id as acid, info_peserta.*,user.*')
            ->join('user', 'info_peserta.userId=user.id')
            ->get()->getResultArray();
        $data = [
            'aktif' => $aktif,
        ];
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/datapengajuan.php', $data);
        echo view('templates/footer');
    }

    public function report($id)
    {
        $userModel = new UserModel();
        $nilaimodel = new NilaiModel();
        $aktif = $nilaimodel->select('report.id as acid, report.*,user.*')
            ->join('user', 'report.idduser=user.id')
            ->where('idduser', $id)
            ->get()->getResultArray();
        $data = [
            'aktif' => $aktif,
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/report.php', $data);
        echo view('templates/footer');
    }

    public function cetakPDF($name, $id)
    {
        $userModel = new UserModel();
        $nilaimodel = new NilaiModel();
        $aktif = $nilaimodel->select('report.id as acid, report.*,user.*')
            ->join('user', 'report.idduser=user.id')
            ->where('idduser', $id)
            ->get()->getResultArray();
        $data = [
            'aktif' => $aktif,
        ];

        $filename = date('y-m-d-H-i-s') . '_' . $name . '_' . $id;
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('admin/pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename);
    }

    public function dashboard()
    {
        $userModel = new UserModel();
        $aktif = $userModel->where('role', 3)->where('user.status', 2)->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))->get()->getResultArray();

        $data = [

            'aktif' => $aktif,

        ];

        echo view('templates/header', $data);
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/halamannilai.php');
        echo view('templates/footer');
    }

    public function lihatnilai()
    {
        $userModel = new UserModel();
        $aktif = $userModel->where('role', 3)
            ->where('status', 2)
            ->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))
            ->get()->getResultArray();
        $nilaimodel = new NilaiModel();
        $nilai = $nilaimodel->findAll();
        $report = $nilaimodel->first();

        $data = [
            'data' => $nilai,
            'report' => $report,
            'aktif' => $aktif,
            'nama' => $this->request->getPost('nama'),
            'jenisKelamin' => $this->request->getPost('JK'),
            'tglLahir' => $this->request->getPost('tglLahir'),
            'email' => $this->request->getPost('email'),
            'role' => 3,
            'status' => 0
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/datanilai.php');
        echo view('templates/footer');
    }

    public function nilai($id = null)
    {
        $nilaimodel = new NilaiModel();
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'pengetahuan' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan tanggal aktivitas dilakukan']
                ],
                'keterampilan' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan jam mulai aktivitas']
                ],
                'kemampuan' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan jam selesai aktivitas']
                ],
                'disiplin' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan detail aktivitas']
                ],
                'total' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan detail aktivitas']
                ]
            ])) {
                session()->setFlashdata('failed', 'Maaf! Terdapat kesalahan dalam pengisian data.');
                return redirect()->to(base_url('dashboard/admin/data/nilai/'))->withInput();
            }
            $nilai = [
                'idduser' => $id,
                'pengetahuan' => $this->request->getPost('pengetahuan'),
                'keterampilan' => $this->request->getPost('keterampilan'),
                'kemampuan' => $this->request->getPost('kemampuan'),
                'disiplin' => $this->request->getPost('disiplin'),
                'total' => $this->request->getPost('total')
            ];

            $nilaimodel = new NilaiModel();
            $nilaimodel->save($nilai);
            session()->setFlashdata('success', 'Sukses! Laporan aktivitas harian berhasil ditambahkan.');
            return redirect()->to(base_url('dashboard/admin/data/nilai'));
        }
        echo view('templates/header');
        echo view('templates/sidebar');
        echo view('templates/topbar');
        echo view('admin/nilai.php');
        echo view('templates/footer');
    }

    public function dashboardtu()
    {
        $model = new Divisi();
        $data  = [
            'content' => $model->getDivisiWithUser()->getResult(),
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/divisi/index', $data);
        echo view('templates/footer');
    }

    public function insert()
    {
        if (isset($_POST['submit'])) {
            $model = new Divisi();
            $data  = [
                'name' => $this->request->getVar('name'),
                'quota' => $this->request->getVar('quota'),
                'pembimbing' => $this->request->getVar('pembimbing')
            ];
            if ($model->where('pembimbing', $data['pembimbing'])->first()) {
                session()->setFlashData('error', 'Pembimbing sudah mempunyai divisi');
                return $this->response->redirect(site_url('dashboard/admin/data/divisi'));
            } else {
                if ($model->save($data)) {
                    $modelUser = new UserModel();
                    $data2 = [
                        'divisi' => $data['name'],
                    ];
                    $modelUser->update($data['pembimbing'], $data2);
                    session()->setFlashData('success', 'Divisi berhasil di tambahkan!');
                    return $this->response->redirect(site_url('dashboard/admin/data/divisi'));
                }
            }
        }
        $model = new UserModel();
        $data = [
            'content' => $model->findPembimbingNull()->getResult(),
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/divisi/add', $data);
        echo view('templates/footer');
    }

    public function update($id)
    {
        if (isset($_POST['submit'])) {
            $model = new Divisi();
            $data  = [
                'name' => $this->request->getVar('name'),
                'quota' => $this->request->getVar('quota'),
                'pembimbing' => $this->request->getVar('pembimbing')
            ];
            $model->update($id, $data);
            session()->setFlashData('success', '');
            return $this->response->redirect(site_url('dashboard/admin/data/divisi'));
        }
        $model = new Divisi();
        $user = new UserModel();
        $data  = [
            'content' => $model->getDivisiWithUserID($id)->getResult(),
            'pembimbing' => $user->findPembimbingNull()->getResult(),
        ];
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/divisi/edit.php', $data);
        echo view('templates/footer');
    }

    public function delete($id)
    {
        $model = new Divisi();
        $model->where('id', $id)->delete();
        session()->setFlashData('success', '');
        return redirect()->to(base_url('dashboard/admin/data/divisi'));
    }

    public function setDivisi($id)
    {
        if (isset($_POST['submit'])) {
            $divisi = new Divisi();
            $user = new UserModel();
            $id_divisi = $this->request->getVar('id_divisi');
            //query nilai kuota
            $quota = $divisi->where('id', $id_divisi)->first();
            $checkpoint = $quota['quota'] > $user->quotaDivisi($id_divisi);
            if ($checkpoint) {
                $model = new UserModel();
                $data = [
                    'divisi' => $id_divisi,
                ];
                $model->update($id, $data);
                session()->setFlashData('success', 'berhasil assigning divisi');
                return $this->response->redirect(site_url('dashboard/admin/peserta'));
            } else {
                session()->setFlashData('error', 'gagal assigning divisi, divisi telah full');
                return $this->response->redirect(site_url('dashboard/admin/peserta'));
            }
        }
        $model = new Divisi();
        $data  = [
            'content' => $model->findAll(),
            'id_user' => $id,
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/divisi/assign', $data);
        echo view('templates/footer');
    }

    public function detailpengajuan($id)
    {
        $userModel = new UserModel();
        $infopesertaModel = new InfoPesertaModel();
        $infopesertaModel->where('id', $id)->first();
        $user = $userModel->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.id', $id)->first();

        $data = [
            'user' => $user
        ];
        echo view('templates/header', $data);
        echo view('templates/sidebarAdmin');
        echo view('templates/topbar');
        echo view('admin/detailPeserta.php');
        echo view('templates/footer');
    }
}
