$(document).ready(function() {
    function fetchBrands() {
        $.ajax({
            url: '../actions/fetch_brand_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // group brands by category
                var grouped = {};
                if (Array.isArray(data)) {
                    data.forEach(function(b) {
                        var cat = b.cat_name || 'Uncategorized';
                        if (!grouped[cat]) grouped[cat] = [];
                        grouped[cat].push(b);
                    });
                }

                var html = '';
                Object.keys(grouped).forEach(function(cat) {
                    html += '<div class="col-12"><h5 class="mt-3">' + $('<div>').text(cat).html() + '</h5></div>';
                    grouped[cat].forEach(function(b) {
                        html += '<div class="col-md-4">\n'
                            + '<div class="card">\n'
                            + '  <div class="card-body">\n'
                            + '    <h6 class="card-title">' + $('<div>').text(b.brand_name).html() + '</h6>\n'
                            
                            + '    <div class="d-flex gap-2">\n'
                            + '      <button class="btn btn-sm btn-warning edit-brand" data-id="' + b.brand_id + '" data-name="' + $('<div>').text(b.brand_name).html() + '">Edit</button>\n'
                            + '      <button class="btn btn-sm btn-danger delete-brand" data-id="' + b.brand_id + '">Delete</button>\n'
                            + '    </div>\n'
                            + '  </div>\n'
                            + '</div>\n'
                            + '</div>';
                    });
                });
                if (html === '') html = '<div class="col-12 text-muted">No brands found.</div>';
                $('#brands-container').html(html);
            },
            error: function() {
                $('#brands-container').html('<div class="col-12 text-danger">Failed to fetch brands.</div>');
            }
        });
    }

    // initial loads
    // populate categories select
    $.ajax({
        url: '../actions/fetch_category_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(cats) {
            var html = '<option value="">Select category</option>';
            if (Array.isArray(cats)) {
                cats.forEach(function(c) {
                    html += '<option value="' + c.cat_id + '">' + $('<div>').text(c.cat_name).html() + '</option>';
                });
            }
            // insert category select into form (if exists)
            if ($('#brand_cat').length) $('#brand_cat').html(html);
        }
    });
    fetchBrands();

    // Add brand
    $('#add-brand-form').submit(function(e) {
        e.preventDefault();
        var brand_name = $('#brand_name').val();
        var brand_cat = $('#brand_cat').val();
        if (!brand_name) {
            alert('Please enter a brand name.');
            return;
        }
        if (!brand_cat) {
            alert('Please select a category.');
            return;
        }
        $.ajax({
            url: '../actions/add_brand_action.php',
            method: 'POST',
            dataType: 'json',
            data: { brand_name: brand_name, brand_cat: brand_cat },
            success: function(resp) {
                    if (resp.status === 'success') {
                        $('#brand_name').val('');
                        $('#brand_cat').val('');
                        fetchBrands();
                    } else {
                    alert(resp.message || 'Failed to add brand');
                }
            },
            error: function() {
                alert('Failed to add brand');
            }
        });
    });

    // Edit brand (prompt)
    $(document).on('click', '.edit-brand', function() {
        var id = $(this).data('id');
        var oldName = $(this).data('name');
        var newName = prompt('Edit brand name:', oldName);
        if (newName && newName !== oldName) {
            $.ajax({
                url: '../actions/update_brand_action.php',
                method: 'POST',
                dataType: 'json',
                data: { brand_id: id, brand_name: newName },
                success: function(resp) {
                    if (resp.status === 'success') {
                        fetchBrands();
                    } else {
                        alert(resp.message || 'Failed to update brand');
                    }
                },
                error: function() {
                    alert('Failed to update brand');
                }
            });
        }
    });

    // Delete brand
    $(document).on('click', '.delete-brand', function() {
        if (!confirm('Are you sure you want to delete this brand?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '../actions/delete_brand_action.php',
            method: 'POST',
            dataType: 'json',
            data: { brand_id: id },
            success: function(resp) {
                if (resp.status === 'success') {
                    fetchBrands();
                } else {
                    alert(resp.message || 'Failed to delete brand');
                }
            },
            error: function() {
                alert('Failed to delete brand');
            }
        });
    });
});