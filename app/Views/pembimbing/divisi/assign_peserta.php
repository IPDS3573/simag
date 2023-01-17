<!-- Begin Page Content -->
<div class="container-fluid">
    <?php $val = Service('validation'); ?>
    <div class="row m-2">
        <div class="col">
            <div class="card card-olive shadow">
                <div class="card-header">
                    <h3 class="card-title">Assign Peserta</h3>
                </div>
                <div class="card-body">
                    <!-- display flash data message -->
                    <form action="<?= base_url('dashboard/pembimbing/data/peserta/assign/peserta/' . $id) ?>" enctype="multipart/form-data" method="POST">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Divisi</label>
                            <select name="id" class="form-control" id="exampleFormControlSelect1">
                                <?php
                                $no = 1;
                                foreach ($content as $row) : ?>
                                    <option value="<?= $row->id ?>"><?= $row->nama ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
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