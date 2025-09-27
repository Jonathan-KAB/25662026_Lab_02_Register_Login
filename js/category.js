$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();
        
        console.log('Form submitted'); // Debug
        
        var email = $('#email').val();
        var password = $('#password').val();
        
        console.log('Email:', email, 'Password:', password); // Debug

        if (email == '' || password == '') {
            alert('Please fill in all fields!');
            return;
        }

        console.log('About to send AJAX request'); // Debug

        $.ajax({
            url: '../actions/login_user_action.php',
            method: 'POST',
            dataType: 'json', // Added to parse JSON
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                console.log('Parsed response:', response); // Debug
                if (response.status === 'success') {
                    alert('Login successful!');
                    window.location.href = 'index.php'; // no dashboard.php set up yet so I made it redirect user to index page, it's in my TO-DO tho
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error occurred'); // Debug
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                alert('Error: ' + error);
            }
        });
    });
});