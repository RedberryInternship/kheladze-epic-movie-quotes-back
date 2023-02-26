<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        body{
            --tw-bg-opacity: 1;
            background-color: rgb(23 23 23 / var(--tw-bg-opacity));
            padding-left: 194px;
            padding-right: 194px;
            color:white;
        }
        .button{
            display: inline-block;
            text-align: center;
            background-color: #E31221;
            width: 128px;
            height:38px;
            margin: auto;
            padding-top: 8px;
            text-decoration: none;
            border-radius: 8px;
            margin-top:32px;
            margin-bottom:40px;
        }
        .header{
            display: block; 
            text-align: center; 
            margin: auto;
            margin-top:77px;
        }
        .header img{
            margin: auto;
            margin-top:9px;
        }
        .header h1{
            display: block; 
            text-align: center; 
            font-size: 12px;
            line-height: 28px;
            color: #DDCCAA;
        }
        .hola{
            margin-top:72px;
            margin-bottom:24px;
        }
        .if{
             margin-bottom:24px;
        }
        .link{     
            word-break: break-all;
            white-space: normal;      
            color:#DDCCAA;
            text-decoration:none;
            width:40%;
        }
        .questions{
            margin-top:40px;
            margin-bottom:24px;
        }
        @media only screen and (max-width: 900px) {
            body{
                padding-left: 35px;
                padding-right: 35px;
            }
        }
    </style>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body style="
            background-color: #171717;
            padding-left: 194px;
            padding-right: 194px;
            color:white;">
    
        <div class="display: block; 
            text-align: center; 
            margin: auto;
            margin-top:77px;
            color:white;" class="header">
            <img style="margin: auto;
            margin-top:9px;" src="{{ asset('images/quote.png') }}" />
            <h1 style=" display: block; 
            text-align: center; 
            font-size: 12px;
            line-height: 28px;
            color: #DDCCAA;">Movie quotes</h1>
        </div>
        <p style="margin-top:72px;
            margin-bottom:24px;" class="hola">Hola {{$name}}</p>
        <p style="display: block; text-align: left;">
        Thanks for joining Movie quotes! We really appreciate it. Please click the button below to reset password:
        </p>
        <a style="display: inline-block;
            text-align: center;
            background-color: #E31221;
            width: 128px;
            height:38px;
            margin: auto;
            padding-top: 12px;
            text-decoration: none;
            border-radius: 8px;
            margin-top:32px;
            margin-bottom:40px;
            color:white;" class="button"
            href="{{ route('password.reset', ['token' => $token]) }}"
            >RESET PASSWORD
        </a>
        <p style="margin-bottom:24px;" class="if">If clicking doesn't work, you can try copying and pasting it to your browser:</p>
        <a style=" word-break: break-all;
            white-space: normal;      
            color:#DDCCAA;
            text-decoration:none;
            width:40%;" class="link" href="{{ route('password.reset', ['token' => $token]) }}">{{ route('password.reset', ['token' => $token]) }}</a>
        <p style="margin-top:40px;
            margin-bottom:24px;" class="questions">If you have any problems, please contact us: support@moviequotes.ge</p>
        <p>MovieQuotes Crew</p>

</body>

</html>
