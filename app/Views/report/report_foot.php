<script>
    var base_url = '<?= base_url() ?>';

    $(document).ready(function() {
        load_chart(myChart);


        $('#area').select2({
            placeholder: "Select Area",
            allowClear: true
        });

        $('#area').select2({
            ajax: {
                url: base_url + '/ajax/get_area',
                type: "post",
                dataType: 'json',
                delay: 100,
                data: function(params) {
                    // CSRF Hash
                    var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                    var csrfHash = $('.txt_csrfname').val(); // CSRF hash

                    return {
                        searchTerm: params.term, // search term
                        [csrfName]: csrfHash // CSRF Token
                    };
                },
                processResults: function(response) {

                    // Update CSRF Token
                    $('.txt_csrfname').val(response.token);

                    return {
                        results: response.data
                    };
                },
                cache: true
            }
        });

        $('#date_from').change(function() {
            set_to();
        });
        $('#date_to').change(function() {
            set_from();
        });


        date_from.max = new Date().toISOString().split("T")[0]
        date_to.max = new Date().toISOString().split("T")[0]

    });

    function set_from() {
        var val = $('#date_to').val();
        if (val != null) {
            date_from.max = new Date(val).toISOString().split("T")[0]
        } else {
            date_from.max = new Date().toISOString().split("T")[0]
        }
    }

    function set_to() {
        // var d = new Date($('#date_from').val());
        // var year = d.getFullYear();
        // var month = d.getMonth();
        // var day = d.getDate();
        // var c = new Date(year + 1, month, day);

        var val = $('#date_from').val();
        if (val != null) {
            date_to.min = new Date(val).toISOString().split("T")[0]
            date_to.max = new Date().toISOString().split("T")[0]
        } else {
            date_to.max = new Date().toISOString().split("T")[0]
        }
        // date_to.max = new Date(c).toISOString().split("T")[0]
    }

    // chart
    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Nilai',
                data: [],
                backgroundColor: [
                    // 'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    // 'rgba(255, 206, 86, 0.2)',
                    // 'rgba(75, 192, 192, 0.2)',
                    // 'rgba(153, 102, 255, 0.2)',
                    // 'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    // 'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    // 'rgba(255, 206, 86, 1)',
                    // 'rgba(75, 192, 192, 1)',
                    // 'rgba(153, 102, 255, 1)',
                    // 'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1,
            }]
        },
        options: {
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        callback: function(value, index, values) {
                            return value + "%";
                        }
                    }
                }
            },
            legend: {
                labels: {
                    fontSize: 25
                }
            },
        },

    });


    // load
    function filter() {
        load_chart(myChart);
    }

    function load_chart(chart) {
        var area = $('#area').val();
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();

        $.ajax({
            method: "POST",
            url: base_url + '/ajax/load_chart',
            data: $('#form-filter').serialize(),
            beforeSend: function(e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(res) {
                console.log(res);

                load_table();

                var tarea = '';
                var tdate = '';

                if (date_from != '' && date_to != '') {
                    tdate = '<b>Tanggal</b> : ' + res.date_from + ' - ' + res.date_to + '.';
                } else if (date_from != '' && date_to == '') {
                    tdate = '<b>Tanggal</b> : ' + res.date_from + ' - largest date in database.';
                } else if (date_from == '' && date_to != '') {
                    tdate = '<b>Tanggal</b> : smallest date in database - ' + res.date_to + '.';
                } else {
                    tdate = '';
                }

                if (area != '') {
                    if (res.labels.length < 5) {
                        var li = '';
                        res.labels.forEach(area => {
                            li += '<li>' + area + '</li>';
                        });
                        tarea = '<b>Area</b> : <br><ul>' + li + '</ul>';
                    } else {
                        tarea = '';
                    }
                }
                $('#text-area').html(tarea);
                $('#text-filter').html(tdate);

                chart.data.labels = res.labels;
                chart.data.datasets[0].data = res.data;
                chart.update();
            }
        });
    }

    // table
    function load_table() {
        var area = $('#area').val();
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();

        $.ajax({
            method: "POST",
            url: base_url + '/ajax/load_table',
            data: $('#form-filter').serialize(),
            beforeSend: function(e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(res) {
                console.log(res);

                var th = '<th>Brand</th>';
                var html_head = '';
                var html_body = '';

                res.get_area.forEach(a => {
                    th += '<th>' + a['area_name'] + '</th>';
                });

                html_head += '<tr>' + th + '</tr>';

                $('#thead').html(html_head);

                res.product_brand.forEach(pb => {
                    var html_nilai = '';

                    res.get_area.forEach(a => {
                        html_nilai += '<td>' + res.nilai[pb['brand_id']][a['area_id']] + '%</td>';
                    });
                    html_body += '<tr>' +
                        '<td>' + pb['brand_name'] + '</td>' +
                        html_nilai +
                        '</tr>';
                });
                $('#tbody').html(html_body);
            }
        });
    }
</script>