<!-- W3hubs.com - Download Free Responsive Website Layout Templates designed on HTML5 
   CSS3,Bootstrap,Tailwind CSS which are 100% Mobile friendly. w3Hubs all Layouts are responsive 
   cross browser supported, best quality world class designs. -->
<!DOCTYPE html>
<html>

<head>
    <title>Reset Password Form In Bootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300i,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #17c0eb;
            font-family: Nunito Sans;
        }

        .btn {
            background-color: #17c0eb;
            width: 100%;
            color: #fff;
            padding: 10px;
            font-size: 18px;
        }

        .btn:hover {
            background-color: #2d3436;
            color: #fff;
        }

        input {
            height: 50px !important;
        }

        .form-control:focus {
            border-color: #18dcff;
            box-shadow: none;
        }

        h3 {
            color: #17c0eb;
            font-size: 36px;
        }

        .cw {
            width: 35%;
        }

        @media(max-width: 992px) {
            .cw {
                width: 60%;
            }
        }

        @media(max-width: 768px) {
            .cw {
                width: 80%;
            }
        }

        @media(max-width: 492px) {
            .cw {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Nhom G</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/profile">Profile <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/change-password">Change Password <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/send-code-verify-email">Verify email account<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-white text-center p-5 mt-3 center">
            @if(auth()->user()->email_verify==0)
            <h3>Verify Email </h3>
            @if(Session::has('success'))
            <div class="alert alert-success">
                {{Session::get('success')}}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
            <form class="pb-3" method="POST">
                @csrf
                <div class="form-group">
                </div>
                <button type="submit" class="btn">Send Mail</button>
            </form>
            @else
            <h3>Email Confirmed </h3>
            @if(Session::has('success'))
            <div class="alert alert-success">
                {{Session::get('success')}}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
            @endif
        </div>
    </div>
</body>

</html>