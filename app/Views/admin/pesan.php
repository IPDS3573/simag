<!-- Begin Page Content -->
<div class="container-fluid">
    <?php $val = Service('validation'); ?>
    <div class="row m-2">
        <div class="col">
            <div class="card card-olive shadow">
                <div class="card-header">
                    <h3 class="card-title">ISI KETERANGAN VALIDASI</h3>
                </div>
                <div class="card-body">
                    <!-- display flash data message -->
                    <?php
                    if (session()->getFlashdata('failed')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('failed') ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?= base_url('dashboard/admin/pengajuan/pesan/' . $content['id']); ?>" enctype="multipart/form-data" method="POST">

                        <div class="form-group">
                            <label for="ket">Isi Informasi Pengajuan</label><small class="text-danger">*</small>
                            <input type="text" name="ket" class="form-control" id="ket" value="<?= $content['ket']; ?>">
                            <?= ($val->hasError('ket')) ? '<small class="text-danger">' . $val->getError('ket') . '</small>' : ''; ?>
                        </div>
                        <div class="form-group">
                            <label for="inputJK">Status</label><br>
                            <select class="form-control" id="inputJK" placeholder="Jenis Kelamin" name="statuspgj">
                                <option value="diterima">Diacc</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="diproses">Diproses</option>
                            </select>
                        </div>

                        <button type=" submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<script>
    $('.alert').delay(5000).slideUp('slow');
</script>
</div>
<!-- End of Main Content -->