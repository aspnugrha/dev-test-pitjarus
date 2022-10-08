<h5 class="mt-5 mb-3">Presentasi laporan produk</h5>

<form class="row g-3 needs-validation mb-2" id="form-filter">
    <?= csrf_field() ?>
    <!-- CSRF token -->
    <input type="hidden" class="txt_csrfname" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

    <div class="col-md-4">
        <select name="area[]" id="area" class="form-control" multiple>
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" name="date_from" id="date_from" placeholder="Select Date From" onfocus="(this.type='date')" onblur="(this.type='text')">
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" name="date_to" id="date_to" placeholder="Select Date To" onfocus="(this.type='date')" onblur="(this.type='text')">
    </div>

    <div class="col-2">
        <a class="btn btn-primary" id="btn-filter" onclick="filter()"><i class="ti-search"></i> View</a>
    </div>

    <!-- <input type="text" id="tgl1" onclick="set_date2(new Date(2020, 10 - 1, 25), new Date())"> -->
    <!-- <input type="text" id="tgl2" onclick="set_date1(new Date(2020, 10 - 1, 25), new Date())"> -->
</form>

<p class="my-1" id="text-filter"></p>
<div class="mb-2" id="text-area"></div><br>

<div class="d-flex justify-content-center row">
    <div class="col-lg-10 col-md-10 col-sm-12 mt-4 mb-4">
        <canvas id="myChart"></canvas>
    </div>
</div>
<br>

<div class="table-responsive my-5">
    <table class="table table-bordered table-striped mt-4" style="width: 100%;">
        <thead id="thead"></thead>
        <tbody id="tbody"></tbody>
    </table>
</div>
<br><br><br><br>