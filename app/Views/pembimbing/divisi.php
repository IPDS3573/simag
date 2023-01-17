<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Tabel data peserta -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <?php if (!empty(session()->getFlashdata('success'))) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty(session()->getFlashdata('error'))) : ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>
            <h4 class="font-weight-bold">Data Peserta <?= $divisi[0]->name ?></h4>
        </div>
        <a class="btn btn-primary" href="<?= base_url('dashboard/pembimbing/data/peserta/assign/peserta/'. $divisi[0]->divisi_id); ?>">Tambah Peserta</a>
        <div class="card-body">
            <!-- tabel data pendaftar -->
            <div class="table-responsive" id="tabel-user">
                <center>
                    <h3>PESERTA</h3>
                </center>
                <br>
                <table class="table table-bordered" id="dataTableuser" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>E-mail</th>
                            <th>Tanggal Lahir</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($user as $key => $value) : ?>
                            <tr>
                                <td><?= $i;
                                    $i++; ?></td>
                                <td><?= $value->nama; ?></td>
                                <td><?= $value->email; ?></td>
                                <td><?= $value->tglLahir; ?></td>
                                <td>
                                    <a href="<?= base_url('dashboard/pembimbing/remove/from/divisi/' . $value->id) ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->