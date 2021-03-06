<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Czatap</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" type="image/png" sizes="192x192"  href="/storage/images/android-icon-192x192.png">

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">  
        
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
        <script>
            navigator.serviceWorker.register('/sw.js').catch(e=>console.error('Ups!' + e))
        </script>
        <link rel="manifest" href="/manifest.json">
    </head>
    <body class="antialiased relative text-center w-full h-screen overflow-x-hidden">
        <div class="app-logo w-full text-center text-white text-7xl mt-8">
            Czatap
        </div>

        <div class="w-screen mt-12 relative flex flex-col justify-center content-center items-center overflow-x-hidden">
            <form class="flex flex-col items-center text-gray-800 py-4 startForm rounded-lg px-2" action="{{ route('user.login') }}" method="POST">
                <div class="w-full text-center text-lg form-text mb-8 mt-4">Zaloguj się</div>
                @csrf
                <div class="input-group relative">
                    <input class="form-input" type="email" name="email" id="email" required onchange="this.setAttribute('value', this.value);"/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Email</label>
                </div>
                <div class="input-group relative">
                    <input class="form-input" type="password" name="password" id="password" required onchange="this.setAttribute('value', this.value);"/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Hasło</label>
                </div>
                <button class="w-36 cta-btn text-gray-800 my-4 px-4 py-2 rounded-md" id="send" type="submit">Wyślij <i class="far fa-paper-plane"></i></button>
                <p class="form-text text-base mt-8 mb-4">Nie masz konta? </br> <a href="{{ route('user.register') }}" class="text-blue-400">Zarejestruj się!</a> </p>
                <p class="form-text text-base mt-4 mb-4">Zapomniałeś hasła? </br> <a href="{{ route('user.forgot_password') }}" class="text-blue-400">Resetuj hasło!</a> </p>
                <div class="flex items-center justify-end mt-4">
                    <a href="{{ url('auth/google/redirect') }}">
                        <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png">
                    </a>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <a href="{{ url('auth/facebook/redirect') }}">
                        <i class="fa-brands fa-facebook" style="color: #3B5499;"></i>
                    </a>
                </div>
            </form>
        </div>
        <div>Icons made by <a href="https://www.freepik.com" rel="nofollow" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" rel="nofollow" title="Flaticon">www.flaticon.com</a></div>

        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '494383099155021',
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v14.0'
                });

                FB.AppEvents.logPageView();

            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    </body>
</html>