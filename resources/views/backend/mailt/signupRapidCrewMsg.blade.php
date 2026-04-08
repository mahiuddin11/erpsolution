<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style type="text/css" rel="stylesheet" media="all">
    /* Media Queries */
    @media only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }
    }
    </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" align="center">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;padding-top:20px;">
                            <img src="{{ asset('backend/assets/image/logo.png') }}" height="300px" width="400px" />
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style="width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;"
                            width="100%">
                            <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0;" align="center"
                                width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="font-family: Arial, &#039;Helvetica Neue&#039;, Helvetica, sans-serif; padding: 35px;">
                                        <!-- Greeting -->
                                        <h1
                                            style="margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;">
                                            New merchent registration in THERAPIDCREW.
                                            <br>
                                            <strong>Full Name: </strong> {{ $merchent->full_name }} <br>
                                            <strong>Username : </strong> {{ $merchent->username }} <br>
                                            <strong>Shop Name: </strong> {{ $merchent->shop_name }} <br>
                                            <strong>Shop Email: </strong> {{ $merchent->shop_email }} <br>
                                            <strong>Shop Address: </strong> {{ $merchent->shop_address }} <br>
                                            <strong>Pickup Phone: </strong> {{ $merchent->pickup_phone }} <br>
                                            <strong>Pickup Address: </strong> {{ $merchent->pickup_address }} <br>
                                        </h1>
                                        <!-- Intro -->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table
                                style="width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;"
                                align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="font-family: Arial, &#039;Helvetica Neue&#039;, Helvetica, sans-serif; color: #AEAEAE; padding: 35px; text-align: center;">
                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                            &copy; {{ date('Y') }}
                                            <a target="_blank" rel="noopener noreferrer" target="_blank"
                                                style="color: #3869D4;" href="{{ config('app.url') }}"
                                                target="_blank">{{ config('app.name') }}</a>.
                                            All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>


                    <!-- Footer -->

                </table>
            </td>
        </tr>
    </table>
</body>

</html>