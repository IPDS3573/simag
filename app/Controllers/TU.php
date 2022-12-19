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
            'content' => $model->index()->getResult(),
        ];
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
                'quota' => $this->request->getVar('quota')
            ];
            $model->save($data);
            session()->setFlashData('success', '');
            return $this->response->redirect(site_url('dashboard/tu/data/divisi'));
        }
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/add');
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
            return $this->response->redirect(site_url('dashboard/tu/data/divisi'));
        }
        $model = new Divisi();
        $data  = [
            'content' => $model->where('id', $id)->first(),
        ];
        echo view('templates/header');
        echo view('templates/sidebarTu');
        echo view('templates/topbar');
        echo view('spadmin/divisi/edit', $data);
        echo view('templates/footer');
    }

    public function delete($id)
    {
        $model = new Divisi();
        $model->where('id', $id)->delete();
        session()->setFlashData('success', '');
        return redirect()->to(base_url('dashboard/tu/data/divisi'));
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
