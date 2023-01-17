<?php

namespace App\Controllers;

use App\Models\AktivitasModel;
use App\Models\Divisi;
use App\Models\UserModel;
use App\Models\AbsenModel;
use App\Models\InfoPesertaModel;
use App\Models\NilaiModel;
use CodeIgniter\API\ResponseTrait;
use Google\Service\AdExchangeBuyerII\Proposal;
use Dompdf\Dompdf;
use Rector\Core\NodeManipulator\FuncCallManipulator;

class Pembimbing extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $userModel = new UserModel();
        $aktivitasModel = new AktivitasModel();
        $nilaimodel = new NilaiModel();
        $nilai = $nilaimodel->findAll();
        $infopesertaModel = new InfoPesertaModel();

        $report = $nilaimodel->first();
        $pesertaAktif = $infopesertaModel->CountAllPesertaAktif();
        $pendaftar = $userModel->where('role', 3)->where('status', 1)->countAllResults();
        $belumsetuju = $aktivitasModel->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 0)->countAllResults();
        $setuju = $aktivitasModel->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 2)->countAllResults();
        $riwayat = $userModel->where('role', 3)->where('user.status', 2)->join('info_peserta', 'user.id=info_peserta.userId')->countAllResults();
        $daftar = $userModel->limit(10)->where('role', 3)->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('user.status', 0)->get()->getResultArray();
        $aktivitas = $aktivitasModel->limit(10)->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 0)->get()->getResultArray();
        $pesAktif = $userModel->where('role', 3)->where('user.status', 2)->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))->get()->getResultArray();;
        $peserta = $userModel->where('role', 3)->where('user.status', 2)->join('info_peserta', 'user.id=info_peserta.userId')->get()->getResultArray();;

        $data = [
            'pendaftar' => $pendaftar,
            'laporan' => $belumsetuju,
            'aktif' => $pesertaAktif,
            'riwayat' => $riwayat,
            'daftar' => $daftar,
            'aktivitas' => $aktivitas,
            'pesAktif' => $pesAktif,
            'peserta' => $peserta,
            'nilai' => $nilai,
            'report' => $report
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/pembimbing.php');
        echo view('templates/footer');
    }

    // Data peserta
    public function dataPeserta()
    {
        $userModel = new UserModel();
        $user = $userModel->findAll();
        $aktif = $userModel->getUserDivisi()->getResultArray();
        $deaktif = $userModel->where('role', 3)->where('status', 3)->get()->getResultArray();
        $pendaftar = $userModel->where('role', 3)->where('status', 1)->get()->getResultArray();

        $data = [
            'pendaftar' => $pendaftar,
            'aktif' => $aktif,
            'deaktif' => $deaktif,
            'user' => $user,
        ];
        //dd($aktif);
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/datapeserta.php', $data);
        echo view('templates/footer');
    }

    public function terimaPeserta($id)
    {
        $userModel = new UserModel();
        $data = [
            'status' => 2
        ];
        $userModel->update($id, $data);
        return redirect()->to(base_url('dashboard/pembimbing/data/peserta'));
    }

    public function hapusPeserta($id)
    {
        $userModel = new UserModel();
        $userModel->where('id', $id)->delete();
        return redirect()->to(base_url('dashboard/pembimbing/data/peserta'));
    }

    public function terimaPesertaDash($id)
    {
        $userModel = new UserModel();
        $data = [
            'status' => 2
        ];
        $userModel->update($id, $data);
        return redirect()->to(base_url('dashboard/pembimbing/data/peserta'));
    }

    public function hapusPesertaDash($id)
    {
        $userModel = new UserModel();
        $userModel->where('id', $id)->delete();
        return redirect()->to(base_url('dashboard/pembimbing/data/peserta'));
    }

    public function detailPeserta($id)
    {
        $userModel = new UserModel();
        $user = $userModel->select('user.*,info_peserta.*,info_peserta.status as status_pengajuan')->join('info_peserta', 'user.id=info_peserta.userId')->where('info_peserta.userId', $id)->first();
        $data = [
            'user' => $user
        ];
        dd($data);
        if (session()->get('role') == 2) {
            echo view('templates/header');
            echo view('templates/sidebarPembimbing');
            echo view('templates/topbar');
            echo view('pembimbing/detailpeserta', $data);
            echo view('templates/footer');
        } elseif (session()->get('role') == 4) {
            echo view('templates/header');
            echo view('templates/sidebarTu');
            echo view('templates/topbar');
            echo view('pembimbing/detailpeserta', $data);
            echo view('templates/footer');
        }
    }
    // My Division
    public function myDivision($name, $id)
    {
        $user = new UserModel();
        $divisi = new Divisi();
        $id_divisi = $divisi->where('name', $name)->first();
        $data = [
            'user' => $user->finduserByDivisi($id_divisi['id'])->getResult(),
            'divisi' => $divisi->getDivisiByNameAndID($name, $id)->getResult(),
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/divisi', $data);
        echo view('templates/footer');
    }

    public function setPeserta($id)
    {
        $model = new UserModel();
        if (isset($_POST['submit'])) {
            $data  = [
                'divisi' => $id,
                'id' => $this->request->getVar('id')
            ];
            $model->update($data['id'], $data);
            session()->setFlashData('success', '');
            return $this->response->redirect(site_url('dashboard/pembimbing/divisi/saya/' . session()->get('divisi') . '/' . session()->get('id')));
        }
        $data = [
            'content' => $model->findPesertaNull()->getResult(),
            'id' => $id
        ];
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/divisi/assign_peserta', $data);
        echo view('templates/footer');
    }

    public function removeFromMyDivision($id)
    {
        $model = new UserModel();
        $data = [
            'divisi' => 'NO DIVISION',
        ];
        $model->update($id, $data);
        session()->setFlashData('success', '');
        return $this->response->redirect(site_url('dashboard/pembimbing/divisi/saya/' . session()->get('divisi') . '/' . session()->get('id')));
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
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/dataabsen.php', $data);
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
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/detailAbsen.php');
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
        $setuju = $aktivitasModel->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 2)->get()->getResultArray();
        $belumsetuju = $aktivitasModel->select('aktivitas.id as acid, aktivitas.*,user.*')->join('user', 'aktivitas.userId=user.id')->where('aktivitas.status', 0)->get()->getResultArray();

        $data = [
            'setuju' => $setuju,
            'belumsetuju' => $belumsetuju
        ];
        //dd(session()->get('role'));
        if (session()->get('role') == 2) {
            echo view('templates/header', $data);
            echo view('templates/sidebarPembimbing');
            echo view('templates/topbar');
            echo view('pembimbing/dataaktivitas.php');
            echo view('templates/footer');
        } elseif (session()->get('role') == 4) {
            echo view('templates/header');
            echo view('templates/sidebarTu');
            echo view('templates/topbar');
            echo view('pembimbing/dataaktivitas.php', $data);
            echo view('templates/footer');
        }
    }

    public function terimaAktivitas($id)
    {
        $aktivitasModel = new AktivitasModel();
        $data = [
            'status' => 2
        ];
        $aktivitasModel->update($id, $data);
        if (session()->get('role') == 2) {
            return redirect()->to(base_url('dashboard/pembimbing/data/aktivitas'));
        } elseif (session()->get('role') == 4) {
            return redirect()->to(base_url('dashboard/tu/data/aktivitas'));
        }
    }

    public function hapusAktivitas($id)
    {
        $aktivitasModel = new AktivitasModel();
        $data = [
            'status' => 3
        ];
        $aktivitasModel->update($id, $data);
        if (session()->get('role') == 2) {
            return redirect()->to(base_url('dashboard/pembimbing/data/aktivitas'));
        } elseif (session()->get('role') == 4) {
            return redirect()->to(base_url('dashboard/tu/data/aktivitas'));
        }
    }
    public function terimaAktivitasDash($id)
    {
        $aktivitasModel = new AktivitasModel();
        $data = [
            'status' => 1
        ];
        $aktivitasModel->update($id, $data);
        if (session()->get('role') == 2) {
            return redirect()->to(base_url('dashboard/pembimbing/data/aktivitas'));
        } elseif (session()->get('role') == 4) {
            return redirect()->to(base_url('dashboard/tu/data/aktivitas'));
        }
    }

    public function hapusAktivitasDash($id)
    {
        $aktivitasModel = new AktivitasModel();
        $data = [
            'status' => 3
        ];
        $aktivitasModel->update($id, $data);
        if (session()->get('role') == 2) {
            return redirect()->to(base_url('dashboard/pembimbing/data/aktivitas'));
        } elseif (session()->get('role') == 4) {
            return redirect()->to(base_url('dashboard/tu/data/aktivitas'));
        }
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
                return redirect()->to(base_url('dashboard/pembimbing/data/nilai/'))->withInput();
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
            return redirect()->to(base_url('dashboard/pembimbing/data/nilai'));
        }
        echo view('templates/header');
        echo view('templates/sidebar');
        echo view('templates/topbar');
        echo view('pembimbing/nilai.php');
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
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/report.php', $data);
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
        $dompdf->loadHtml(view('pembimbing/pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename);
    }

    public function dashboard()
    {
        $userModel = new UserModel();
        $aktif = $userModel->where('role', 3)
            ->where('user.status', 2)
            ->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))
            ->get()->getResult();

        $data = [

            'aktif' => $aktif,

        ];
        // dd($data);
        echo view('templates/header', $data);
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/halamannilai.php');
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
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/datanilai.php');
        echo view('templates/footer');
    }

    public function pesan($id)
    {
        $infopesertaModel = new InfoPesertaModel();
        $data = [
            'content' => $infopesertaModel->where('id', $id)->first(),
        ];
        //dd($id);
        if (isset($_POST['submit'])) {
            if (!$this->validate([
                'ket' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Masukkan detail kegiatan']
                ],
            ])) {
                session()->setFlashdata('failed', 'Maaf! Terdapat kesalahan dalam pengisian data.');
                return redirect()->to(base_url('dashboard/pembimbing/data/peserta/pesan/' . $id))->withInput();
            }
            $data = [
                'id' => $id,
                'ket' => $this->request->getPost('ket'),
                'status' => $this->request->getPost('status')
            ];

            $infopesertaModel->update($id, $data);
            session()->setFlashdata('success', 'Sukses! Anda berhasil menambahkan data.');
            return redirect()->to(base_url('dashboard/pembimbing'));
        }

        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/pesan.php', $data);
        echo view('templates/footer');
    }

    public function dataPengajuan()
    {
        $infoPesertaModel = new InfoPesertaModel();
        $aktif = $infoPesertaModel->select('info_peserta.id as acid, info_peserta.*,user.*')
            ->join('user', 'info_peserta.userId=user.id')
            ->get()->getResultArray();
        $data = [
            'aktif' => $aktif,
        ];
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/datapengajuan.php', $data);
        echo view('templates/footer');
    }

    public function insert()
    {
        if (isset($_POST['submit'])) {
            $model = new Divisi();
            $data  = [
                'name' => $this->request->getVar('name'),
                'quota' => $this->request->getVar('quota')
            ];
            $model->save($data);
            session()->setFlashData('success', '');
            return $this->response->redirect(site_url('dashboard/pembimbing/data/divisi'));
        }
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/divisi/add');
        echo view('templates/footer');
    }

    public function update($id)
    {
        if (isset($_POST['submit'])) {
            $model = new Divisi();
            $data  = [
                'name' => $this->request->getVar('name'),
                'quota' => $this->request->getVar('quota')
            ];
            $model->update($id, $data);
            session()->setFlashData('success', '');
            return $this->response->redirect(site_url('dashboard/pembimbing/data/divisi'));
        }
        $model = new Divisi();
        $data  = [
            'content' => $model->where('id', $id)->first(),
        ];
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/divisi/edit.php', $data);
        echo view('templates/footer');
    }

    public function delete($id)
    {
        $model = new Divisi();
        $model->where('id', $id)->delete();
        session()->setFlashData('success', '');
        return redirect()->to(base_url('dashboard/pembimbing/data/divisi'));
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
                return $this->response->redirect(site_url('dashboard/pembimbing/data/peserta'));
            } else {
                session()->setFlashData('error', 'gagal assigning divisi, divisi telah full');
                return $this->response->redirect(site_url('dashboard/pembimbing/data/peserta'));
            }
        }
        $model = new Divisi();
        $data  = [
            'content' => $model->findAll(),
            'id_user' => $id,
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/divisi/assign', $data);
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
        echo view('templates/sidebarPembimbing');
        echo view('templates/topbar');
        echo view('pembimbing/detailPeserta.php');
        echo view('templates/footer');
    }
}
