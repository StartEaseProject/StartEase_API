<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <div style="line-height:1.5; font-family:'Golos Text', sans-serif; font-family:'Golos Text', sans-serif; font-size:1rem; text-align:center">
        <main style="padding-block:2rem; padding-inline:2rem">
            <h1 style="color:#6F88FC; font-size:2rem; font-weight:bold">Project Submission</h1>
            <p style="color:#191A44">Dear StartEase User, a project entitled {{ $project->trademark_name }} submission has been added by your name to our platform.
                You are the project holder of this project. Here is a link to the
                @if ($user_type === 'App\Models\Student')
                <a href="{{env('PROJECT_PAGE_STUDENT')}}">project page</a>
                @else
                <a href="{{env('PROJECT_PAGE').$project->id}}">project page</a>
                @endif
            </p>
        </main>
        <footer style="background-color:#6F88FC; display:grid; justify-items:center; padding:1rem">
            <p style="color:white">Copyrights for StartEase Platform 2022-2023</p>
        </footer>
    </div>
</body>

</html>