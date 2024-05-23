<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <div style="line-height:1.5; font-family:'Golos Text', sans-serif; font-family:'Golos Text', sans-serif; font-size:1rem; text-align:center">
        <main style="padding-block:2rem; padding-inline:2rem">
            <h1 style="color:#6F88FC; font-size:2rem; font-weight:bold">Defence Invitation</h1>
            @if ($user_type === 'App\Models\Student')
            <p style="color:#191A44">
                Dear StartEase User, the defence of the project entitled {{ $defence->project->trademark_name }} is schedules for the {{ $defence->date }} at {{ $defence->time }}.
                Here is a link to the <a href="{{env('DEFENCE_PAGE_STUDENT').$defence->id}}">defence page</a>
            </p>

            @else
            <p style="color:#191A44">
                Dear StartEase User, you have been invited to be a jury {{ $role }} in the defence of the project entitled {{ $defence->project->trademark_name }}.
                The defence is schedules for the {{ $defence->date }} at {{ $defence->time }}.
                If you are not registered yet, please click the link in the other mail to complete the registration process. Here is a link to the
                <a href="{{env('DEFENCE_PAGE').$defence->id}}">defence page</a>
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