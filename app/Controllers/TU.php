<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\Divisi;
use App\Controllers\BaseController;

class TU extends BaseController
{
    public function index()
    {
        $model = new Divisi();
        $data  = [
            'content' => $model->getDivisiWithUser()->getResult(),
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/index', $data);
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
                return $this->response->redirect(site_url('dashboard/tu/data/divisi'));
            } else {
                if ($model->save($data)) {
                    $modelUser = new UserModel();
                    $data2 = [
                        'divisi' => $data['name'],
                    ];
                    $modelUser->update($data['pembimbing'], $data2);
                    session()->setFlashData('success', 'Divisi berhasil di tambahkan!');
                    return $this->response->redirect(site_url('dashboard/tu/data/divisi'));
                }
            }
        }
        $model = new UserModel();
        $data = [
            'content' => $model->findPembimbingNull()->getResult(),
        ];
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/add', $data);
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
            return $this->response->redirect(site_url('dashboard/tu/data/divisi'));
        }
        $model = new Divisi();
        $user = new UserModel();
        $data  = [
            'content' => $model->getDivisiWithUserID($id)->getResult(),
            'pembimbing' => $user->findPembimbingNull()->getResult(),
        ];
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/edit', $data);
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
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/report.php', $data);
        echo view('templates/footer');
    }

    public function nilai()
    {
        $userModel = new UserModel();
        $aktif = $userModel->where('role', 3)->where('user.status', 2)->join('info_peserta', 'user.id=info_peserta.userId')
            ->where('info_peserta.endDate>', date('Y-m-d'))->get()->getResultArray();

        $data = [

            'aktif' => $aktif,

        ];
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/nilai.php', $data);
        echo view('templates/footer');
    }

    public function nilaiSiswa($id = null)
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
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/nilaisiswa.php');
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

    public function delete($id, $user_id)
    {
        $model = new Divisi();
        $user = new UserModel();
        if ($model->where('id', $id)->delete()) {
            $data = [
                'divisi' => 'NO DIVISION'
            ];
            //dd($user_id);
            if ($user->update($user_id, $data)) {
                session()->setFlashData('success', '');
                return redirect()->to(base_url('dashboard/tu/data/divisi'));
            } else {
                echo 'error';
            }
        }
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
                return $this->response->redirect(site_url('dashboard/tu/data/peserta'));
            } else {
                session()->setFlashData('error', 'gagal assigning divisi, divisi telah full');
                return $this->response->redirect(site_url('dashboard/tu/data/peserta'));
            }
        }
        $model = new Divisi();
        $data  = [
            'content' => $model->findAll(),
            'id_user' => $id,
        ];
        //dd($data);
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/assign', $data);
        echo view('templates/footer');
    }
}
