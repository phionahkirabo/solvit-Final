<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-header">Register</h2>
        <form id="registerForm">
            <div class="mb-3">
                <label for="hod_name" class="form-label">HOD Name</label>
                <input type="text" class="form-control" id="hod_name" name="hod_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
        <div id="responseMessage" class="mt-3"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#registerForm').submit(function (e) {
                e.preventDefault();
                
                let formData = {
                    hod_name: $('#hod_name').val(),
                    email: $('#email').val(),
                    contact_number: $('#contact_number').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val()
                };
                
                $.ajax({
                    url: "https://aqs-project-tracking-cca8cddeaa55.herokuapp.com/api/register",
                    method: "POST",
                    data: JSON.stringify(formData),
                    contentType: "application/json",
                    success: function (response) {
                        $('#responseMessage').html('<div class="alert alert-success">Registration successful!</div>');
                    },
                    error: function (response) {
                        $('#responseMessage').html('<div class="alert alert-danger">Error: ' + response.responseJSON.message + '</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>