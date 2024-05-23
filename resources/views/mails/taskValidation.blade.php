<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <div style="line-height:1.5; font-family:'Golos Text', sans-serif; font-family:'Golos Text', sans-serif; font-size:1rem; text-align:center">
        <main style="padding-block:2rem; padding-inline:2rem">
            <h1 style="color:#6F88FC; font-size:2rem; font-weight:bold">TASK VALIDATION</h1>
            @if ($task->status!=="completed")
            <p style="color:#191A44">Dear StartEase User, the task <b>{{ $task->title }}</b> of the project
                <b>{{ $task->project->trademark_name }}</b> was accepted by the supervisor. Here is a link to the
                @if ($user_type === 'App\Models\Student')
                <a href="{{env('TASK_PAGE_STUDENT').$task->project_id.'/tasks/'.env('$task->id')}}">task page</a>
                @else
                <a href="{{env('TASK_PAGE').$task->project_id.'/tasks/'.env('$task->id')}}">task page</a>
                @endif
            </p>
            @else
            <p style="color:#191A44">Dear StartEase User, the task <b>{{ $task->title }}</b> of the project
                <b>{{ $task->project->trademark_name }}</b> was rejected. Members of this project must submit
                another time the requested resources. Here is a link to the
                @if ($user_type === 'App\Models\Student')
                <a href="{{env('TASK_PAGE_STUDENT').$task->project_id.'/tasks/'.env('$task->id')}}">task page</a>
                @else
                <a href="{{env('TASK_PAGE').$task->project_id.'/tasks/'.env('$task->id')}}">task page</a>
                @endif
            </p>
            @endif
        </main>
        <footer style="background-color:#6F88FC; display:grid; justify-items:center; padding:1rem">
            <p style="color:white">Copyrights for StartEase Platform 2022-2023</p>
        </footer>
    </div>
</body>

</html>