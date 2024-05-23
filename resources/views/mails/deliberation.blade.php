<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <div style="line-height:1.5; font-family:'Golos Text', sans-serif; font-family:'Golos Text', sans-serif; font-size:1rem; text-align:center">
        <main style="padding-block:2rem; padding-inline:2rem">
            <h1 style="color:#6F88FC; font-size:2rem; font-weight:bold">Deliberation Release</h1>
            <p style="color:#191A44">Dear StartEase User, the jury has released the deliberationof the project entitled {{ $defence->project->trademark_name }}.
                You can view the deliberation details here . Here is a link to the
                @if ($user_type === 'App\Models\Student')
                <a href="{{env('DELIBERATION_PAGE_STUDENT').$defence->id.'/deliberation'}}">deliberation page</a>
                @else
                <a href="{{env('DELIBERATION_PAGE').$defence->id.'/deliberation'}}">deliberation page</a>
                @endif
            </p>
            <p style="color:#191A44">If you're not concerned by this invitation, please ignore this email.</p>
        </main>
        <footer style="background-color:#6F88FC; display:grid; justify-items:center; padding:1rem">
            <p style="color:white">Copyrights for StartEase Platform 2022-2023</p>
        </footer>
    </div>
</body>

</html>