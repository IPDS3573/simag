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
            <h4 class="font-weight-bold">Data Peserta PKL</h4>
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" aria-current="page" href="#" id="user">Semua Peserta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" aria-current="page" href="#" id="pendaftar">Pendaftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" aria-current="page" href="#" id="aktif">Peserta Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" aria-current="page" href="#" id="deaktif">Peserta Deaktif</a>
                </li>

            </ul>
        </div>

        <div class="card-body">
            <!-- tabel data pendaftar -->
            <div class="table-responsive" id="tabel-user">
                <center>
                    <h3>DATA SELURUH PESERTA</h3>
                </center>
                <br>
                <table class="table table-bordered" id="dataTableuser" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>E-mail</th>
                            <th>Tanggal Lahir</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($user as $key => $value) : ?>
                            <tr>
                                <td><?= $i;
                                    $i++; ?></td>
                                <td><?= $value['nama']; ?></td>
                                <td><?= $value['email']; ?></td>
                                <td><?= $value['tglLahir']; ?></td>

                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <div class="card-body">
                <!-- tabel data pendaftar -->
                <div class="table-responsive" id="tabel-pendaftar">
                    <center>
                        <h3>DATA SELURUH PESERTA PENDAFTAR</h3>
                    </center>
                    <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>E-mail</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($pendaftar as $key => $value) : ?>
                                <tr>
                                    <td><?= $i;
                                        $i++; ?></td>
                                    <td><a href="<?= base_url('dashboard/admin/peserta/detail/' . $value['id']) ?>"><?= $value['nama']; ?></a></td>
                                    <td><?= $value['email']; ?></td>

                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <br>
                <!-- tabel data peserta aktif -->
                <div class="table-responsive" id="tabel-aktif" style="display: none;">
                    <center>
                        <h3>DATA SELURUH PESERTA AKTIF</h3>
                    </center>
                    <table class="table table-bordered" id="dataTable3" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>E-mail</th>
                                <th>Divisi</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($aktif as $key => $value) : ?>
                                <tr>
                                    <td><?= $i;
                                        $i++; ?></td>
                                    <td><a href="<?= base_url('dashboard/admin/peserta/detail/' . $value['id']) ?>"><?= $value['nama']; ?></a></td>
                                    <td><?= $value['email']; ?></td>
                                    <td><?= $value['nama_divisi']; ?></td>
                                    <td>
                                        <a href="<?= base_url('dashboard/admin/data/peserta/assign/divisi/' . $value['id']) ?>" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                    </td>

                                </tr>

                            <?php endforeach ?>
                        </tbody>
                    </table>

                </div>
                <br>
                <br>
                <!-- tabel data peserta yang sudah tidak aktif -->
                <div class="table-responsive" id="tabel-deaktif" style="display: none;">
                    <center>
                        <h3>DATA SELURUH PESERTA DEAKTIF</h3>
                    </center>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>E-mail</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($deaktif as $key => $value) : ?>
                                <tr>
                                    <td><a href="<?= base_url('dashboard/admin/peserta/detail/' . $value['id']) ?>"><?= $value['nama']; ?></a></td>
                                    <td><?= $value['email']; ?></td>

                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->