<html>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        </head>
    <body>
        <div style="line-height:1.5; font-family:'Golos Text', sans-serif; font-family:'Golos Text', sans-serif; font-size:1rem; text-align:center">
            <main style="padding-block:2rem; padding-inline:2rem">
                <h1 style="color:#6F88FC; font-size:2rem; font-weight:bold">Reset Your Password</h1>
                <p style="color:#191A44">Trouble signing in? Resetting your password is easy. Just enter this code in the website and you will be able
                    to reset your password.
                </p>
                <div style="text-align:center; margin:2rem 0rem">
                    @foreach (str_split($code) as $char)
                        <span style="background-color:#6F88FC; margin-right:5px; border-radius:10px; color:white; font-weight:bold; padding:12px 15px">{{ $char }}</span>
                    @endforeach
                </div>
                <p style="color:#191A44">If you didn’t make this request, then you can ignore this email.</p>
            </main>
            <footer style="background-color:#6F88FC; display:grid; justify-items:center; padding:1rem">
                <p style="color:white">Copyrights for StartEase Platform 2022-2023</p>
            </footer>
        </div>
    </body>
</html>