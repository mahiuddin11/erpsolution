<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: none;
        }

        :root {
            --main-color: #fff;
            --second-color: #347deb;
            --box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            --facebook-color: rgb(60, 90, 154);
            --google-color: rgb(220, 74, 61);
        }

        html {
            height: 100%;
        }

        body {
            background-image: linear-gradient(310deg, #df98fa, #9055ff);
            font-family: sans-serif;
        }

        #container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--main-color);
            width: 828px;
            height: 482px;
            border-radius: 10px;
            display: grid;
            grid-template-columns: repeat(2, 50%);
            box-shadow: var(--box-shadow);
            transition-duration: 1s;
        }

        #left,
        #right {
            background: #fbfbfb;
            margin: auto;
            width: 95%;
            height: 96%;
            border-radius: 10px;
        }

        #left {
            background-size: cover;
            background-position: center;
            box-shadow: var(--box-shadow);
        }

        #welcome,
        #lorem {
            margin: 20px;
            text-shadow: var(--box-shadow);
        }

        #welcome {
            font-size: 75px;
            font-weight: 300;
            margin-top: 330px;
            text-shadow: var(--box-shadow);
        }

        #login {
            padding-top: 35%;
            text-align: center;
            text-transform: uppercase;
            font-weight: 100;
            text-shadow: var(--box-shadow);
        }

        .client-info {
            display: block;
            margin: 20px auto;
            width: 60%;
            height: 50px;
            border: solid #999 1px;
            border-radius: 5px;
            text-indent: 15px;
            transition: all 200ms;
            box-shadow: var(--box-shadow);
        }

        .client-info:focus {
            width: 80%;
        }

        label {
            position: absolute;
            margin: -76px 130px;
            font-size: 12px;
            white-space: nowrap;
            background: #fff;
            padding: 0 5px;
            color: #999;
            transition: all 200ms;
            text-shadow: var(--box-shadow);
        }

        #email:focus~label[for="email"] {
            margin: -76px 70px;
        }

        #password:focus~label[for="password"] {
            margin: -76px 70px;
        }

        #submit {
            border: none;
            background-color: #9055ff;
            color: white;
            width: 60%;
        }

        #submit:hover {
            background-color: #df98fa;
        }

        .social {
            background-color: #fff;
            display: block;
            margin: 10px auto;
            width: 70%;
            height: 50px;
            border: none;
            border-radius: 5px;
            text-transform: uppercase;
            transition-duration: 200ms;
            box-shadow: var(--box-shadow);
            text-shadow: var(--box-shadow);
        }

        #facebook {
            border: solid var(--facebook-color) 1px;
            color: var(--facebook-color);
        }

        #facebook:hover {
            background-color: var(--facebook-color);
            color: white;
        }

        #google {
            border: solid var(--google-color) 1px;
            color: var(--google-color);
        }

        #google:hover {
            background-color: var(--google-color);
            color: white;
        }

        @media (max-width: 750px) {

            #container {
                width: 600px;
                display: block;
            }

            #left {
                display: none;
            }

            #right {
                margin-top: 16px;
                background: #fbfbfb;
                background-size: cover;
                background-position: center;
                box-shadow: var(--box-shadow);
            }

        }

 /* Global styles remain the same */

@media (max-width: 750px) {
    #container {
        width: 90%;
        display: block;
        height: auto; /* Allow height to adjust automatically */
    }

    #left {
        display: block; /* Make #left visible */
        width: 100%;
        margin: 0 auto 20px auto; /* Add some spacing */
        background: #fbfbfb;
        background-size: cover;
        background-position: center;
        box-shadow: var(--box-shadow);
        text-align: center; /* Center align text */
    }

    #right {
        width: 100%;
        margin: 0 auto;
        background: #fbfbfb;
        background-size: cover;
        background-position: center;
        box-shadow: var(--box-shadow);
    }

    #login {
        padding-top: 20px;
    }

    .client-info {
        width: 80%; /* Make input fields more flexible */
        height: 45px; /* Adjust height for smaller screens */
    }

    .password-container {
        width: 80%; /* Adjust width */
    }

    ul {
        padding-left: 0; /* Remove default padding */
    }

    li {
        text-align: left;
        padding-left: 20px;
    }
}

@media (max-height: 850px) {
    #container {
        width: 90%;
        height: auto; /* Adjust height for smaller screens */
    }

    #login {
        padding-top: 10%;
    }

    #welcome {
        margin-top: 240px;
        font-size: 40px;
    }

    #lorem {
        font-size: 15px;
    }

    .client-info {
        width: 90%; /* Make input fields more flexible */
    }
}


        .invalid-feedback {
            display: flex;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            color: red;
        }

        .password-container {
            position: relative;
            width: 60%;
            margin: 20px auto;
            border: solid #999 1px;
            border-radius: 5px;
            box-shadow: var(--box-shadow);
            transition: all 200ms;
        }

        .password-container input {
            width: 100%;
            height: 50px;
            padding-left: 15px;
            border: none;
            border-radius: 5px;
            outline: none;
            box-shadow: none;
        }

        .password-container i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .password-container input:focus {
            width: 100%;
        }

        .client-info {
    position: relative;
    padding-top: 20px; /* Make space for the label */
}


table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

    </style>
</head>

<!-- Change code below this line -->

<body>
    <div id="container">
        <div id="left">
            <img src="https://xtreem.linnasgroup.com/wp-content/uploads/2024/08/Xtreem_Logo-removebg-preview-1.png"
                alt="" style="margin-left: 35px;">
            <ul style="margin-left: 40px;margin-top: 62px;">
                <li style="list-style-type: none;padding:15px">
                    <i class="fas fa-phone" id="togglePassword"></i> +8801958-222208
                </li>
                <li style="list-style-type: none;padding:15px">
                    <i class="fa fa-globe" id="togglePassword"></i> www.itwaybd.com
                </li>
                <li style="list-style-type: none;padding:15px">
                    <i class="fa fa-envelope" id="togglePassword"></i> support@itwaybd.com
                </li>
                <li style="list-style-type: none; padding: 15px; display: flex; align-items: center;">
                    <i class="fa fa-map-marker" id="togglePassword" style="margin-right: 10px;"></i>
                    House-01, Road-1, Sector-5, Uttara, Dhaka
                </li>
            </ul>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div id="right">
                @php
                    use App\Helpers\Helper;
                    $company = \App\Models\Company::where('status', 'Active')->first();
                @endphp

                @if (isset($company['logo']))
                    <img id="login" style="display: block;margin-left: auto;margin-right: auto;"
                        src="{{ asset('/backend/logo/' . ($company['logo'] ?? 0)) }}" class="img-thumbnail "
                        alt="Responsive image">
                @else
                    <h6 id="login" style="color: red">
                        ops! your logo missing
                    </h6>
                @endif
                <input type="email" id="email" name="email" class="client-info">
                <label for="email">Email</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="password" id="password" name="password" class="client-info">

                <label for="password">Password</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <p style="text-align: center;">
                    <a href="{{ route('password.request') }}">I forgot my password</a>
                </p>
                <input type="submit" id="submit" class="client-info" value="Submit">
            </div>
            
        </form>
        
    </div>

      
    <script>
        /* Work in proggress */
        document.getElementById('see-button').addEventListener('click', evt => {
            document.getElementById('blur-work').style.display = 'none';
        })

        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        /* Work in proggress */
    </script>
</body>
<!-- Change code above this line -->


</html>
