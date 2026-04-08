<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Water Technology BD Ltd</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family: 'Roboto', sans-serif;
            background: linear-gradient(310deg, #05693a, #05693a);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;

        }

        .wrapper {

            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 380px;
            padding: 35px;

        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            max-width: 140px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* floating input */

        .form-group {
            position: relative;
            margin-bottom: 22px;
        }

        .form-group input {

            width: 100%;
            padding: 12px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            background: transparent;
            outline: none;

        }

        .form-group label {

            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-40%);
            background: #fff;
            padding: 0 5px;
            color: #777;
            font-size: 14px;
            transition: .3s;
            pointer-events: none;

        }

        .form-group input:focus+label,
        .form-group input:valid+label {

            top: -4px;
            font-size: 12px;
            color: #05693a;

        }

        .form-group input:focus {
            border-color: #05693a;
        }

        /* button */

        button {

            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            background: #05693a;
            color: white;
            font-size: 15px;
            cursor: pointer;
            transition: .3s;

        }

        button:hover {
            background: #044e2c;

        }

        .credentials-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 13px;
        }

        .credentials-table th,
        .credentials-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .credentials-table th {
            background: #f7f7f7;
        }

        .credentials-table tr {
            cursor: pointer;
        }

        /* responsive */

        @media(max-width:768px) {

            .wrapper {

                padding: 25px;

            }

            .logo img {

                max-width: 120px;

            }

        }
    </style>

</head>

<body>

    <div class="wrapper">

        <div class="logo">

            @php
                $company = \App\Models\Company::where('status', 'Active')->first();
            @endphp

            @if (isset($company['logo']))
                <img src="{{ asset('/backend/logo/' . $company['logo']) }}">
            @else
                <h4 style="color:red">Logo Missing</h4>
            @endif

        </div>

        {{-- <h2>Login</h2> --}}

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <div class="form-group">
                <input type="text" name="email" id="email" required>
                <label>Email</label>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" required>
                <label>Password</label>
            </div>

            <button type="submit">Sign In</button>

        </form>

        @if (env('APP_ENV') == 'local')
            <table class="credentials-table">

                <tr>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                </tr>

                <tr data-email="info@xtreem.com" data-password="12345678">
                    <td>info@xtreem.com</td>
                    <td>12345678</td>
                    <td>Admin</td>
                </tr>

            </table>
        @endif

    </div>

    <script>
        document.querySelectorAll('.credentials-table tr').forEach(row => {

            row.addEventListener('click', function() {

                const email = this.getAttribute('data-email');
                const password = this.getAttribute('data-password');

                if (email) {

                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;

                }

            });

        });
    </script>

</body>

</html>
