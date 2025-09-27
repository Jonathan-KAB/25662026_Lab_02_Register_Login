$(document).ready(function() {
    // Fetch and display categories
    function fetchCategories() {
        $.ajax({
            url: '../actions/fetch_category_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var tbody = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(cat) {
                        tbody += '<tr>' +
                            '<td>' + $('<div>').text(cat.cat_name).html() + '</td>' +
                            '<td>' +
                                '<button class="btn btn-sm btn-warning edit-category" data-id="' + cat.cat_id + '" data-name="' + $('<div>').text(cat.cat_name).html() + '">Edit</button> ' +
                                '<button class="btn btn-sm btn-danger delete-category" data-id="' + cat.cat_id + '">Delete</button>' +
                            '</td>' +
                        '</tr>';
                    });
                } else {
                    tbody = '<tr><td colspan="2" class="text-muted">No categories found.</td></tr>';
                }
                $('#category-table tbody').html(tbody);
            },
            error: function(xhr, status, error) {
                $('#category-table tbody').html('<tr><td colspan="2" class="text-danger">Failed to fetch categories.</td></tr>');
            }
        });
    }

    // Initial fetch
    fetchCategories();

    // Add category via AJAX
    $('#add-category-form').submit(function(e) {
        e.preventDefault();
        var category_name = $('#category_name').val();
        if (!category_name) {
            alert('Please enter a category name.');
            return;
        }
        $.ajax({
            url: '../actions/add_category_action.php',
            method: 'POST',
            data: { category_name: category_name },
            success: function() {
                $('#category_name').val('');
                fetchCategories();
            },
            error: function() {
                alert('Failed to add category.');
            }
        });
    });

    // Delete category via AJAX
    $(document).on('click', '.delete-category', function() {
        if (!confirm('Are you sure you want to delete this category?')) return;
        var cat_id = $(this).data('id');
        $.ajax({
            url: '../actions/delete_category_action.php',
            method: 'POST',
            data: { cat_id: cat_id },
            success: function() {
                fetchCategories();
            },
            error: function() {
                alert('Failed to delete category.');
            }
        });
    });
    // Edit category (show prompt, then send update)
    $(document).on('click', '.edit-category', function() {
        var cat_id = $(this).data('id');
        var old_name = $(this).data('name');
        var new_name = prompt('Edit category name:', old_name);
        if (new_name && new_name !== old_name) {
            $.ajax({
                url: '../actions/update_category_action.php',
                method: 'POST',
                data: { cat_id: cat_id, cat_name: new_name },
                success: function() {
                    fetchCategories();
                },
                error: function() {
                    alert('Failed to update category.');
                }
            });
        }
    });
});