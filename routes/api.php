<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefenceController;
use App\Http\Controllers\DeliberationController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\TaskController;
use App\Http\Resources\DefenceResource;
use App\Http\Resources\DeliberationResource;
use App\Http\Resources\ProjectResource;
use App\Models\Announcement;
use App\Models\Defence;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great
|
*/



Route::controller(AuthController::class)->group(function () {
    Route::get('/auth', 'getAuth')->middleware('auth:sanctum');
    Route::post('/authenticate', 'authenticate')->middleware('guest');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::get('/landing', [DashboardController::class, 'landing']);
Route::get('/announcements/public/{id}', [DashboardController::class, 'show_announcement']);

Route::prefix('/register')->middleware('guest')->controller(RegisterController::class)->group(function () {
    Route::post('/', 'initialRegister');
    Route::get('/verify/{payload}', 'verifyToken');
    Route::post('/complete/{payload}', 'completeRegister');
    Route::prefix('/phonenumber')->group(function () {
        Route::post('/{payload}', 'setPhoneNumber');
        Route::post('/otp/{payload}', 'verifyPhoneNumber');
    });
});

Route::prefix("/forgotpassword")->middleware('guest')->controller(ResetPasswordController::class)->group(function () {
    Route::post('/send', 'sendCode');
    Route::post('/verify', 'verifyCode');
    Route::post('/reset', 'resetPassword');
});

Route::get('/announcements/public', [AnnouncementController::class, 'index_public']);


Route::middleware(['auth:sanctum'])->group(function () 
{
    Route::get('/projects/params', [ProjectController::class, 'getParams']);

    Route::post('/users', [RegisterController::class, 'store'])->middleware('create-user');
    Route::prefix("/users")->controller(UserController::class)->group(function () 
    {
        Route::get('/', 'index')->middleware('can:read-user');
        Route::get('/{id}', 'show')->middleware('can:read-user') ;
        Route::get('/{id}/roles', 'getRoles')->middleware('can:read-role');

        Route::put('/enable/{id}', 'enable')->middleware('can:update-user');
        Route::put('/disable/{id}', 'disable')->middleware('can:update-user');

        Route::prefix('/update')->group(function () 
        {
            Route::put('/password', 'updatePassword');
            Route::post('/photo', 'updatePhoto');
            Route::prefix('/phonenumber')->group(function () {
                Route::post('/', 'updatePhoneNumber');
                Route::post('/otp', 'verifyPhoneNumber');
            });
            Route::put('/roles', 'updateRoles')->middleware('can:update-user');
        });
    });

    Route::prefix('/roles')->controller(RoleController::class)->group(function () 
    {
        Route::get('/', 'index')->middleware('can:read-role');
        Route::get('/{id}', 'show')->middleware('can:read-role');
        Route::post('/', 'store')->middleware('can:create-role');
        Route::put('/', 'update')->middleware('can:update-role');
        Route::delete('/{id}', 'destroy')->middleware('can:delete-role');
    });

    Route::prefix('/permissions')->controller(PermissionController::class)->group(function () {
        Route::get('/', 'index')->middleware('can:read-permission');
        Route::post('/', 'store')->middleware('can:create-permission');
        Route::delete('/{id}', 'destroy')->middleware('can:delete-permission');
    });

    Route::prefix('/projects')->controller(ProjectController::class)->group(function () {
        //Route::get('/', 'index');
        Route::get('/my', 'auth')->middleware('can:read-project');
        Route::get('/{id}', 'show')->middleware('can:read-project');
        Route::get('/{id}/progress', 'get_progress')->middleware('can:read-project-observation');
        Route::put('/status', 'changeStatus')->middleware('can:validate-project');
        Route::post('/', 'store')->middleware('can:create-project');
        Route::put('/{id}', 'update')->middleware('can:update-project');
        Route::put('/{id}/progress', 'updateProgress')->middleware('can:update-progress-project');
        Route::delete('/{id}', 'destroy')->middleware('can:withdraw-project');
        Route::put('/{id}/authorize', 'authorize_defence')->middleware('can:authorize-project');
    });

    Route::prefix('/remarks')->controller(RemarkController::class)->group(function () {
        Route::post('/', 'store')->middleware('can:create-remark');
        Route::get('/project/{id}', 'projectRemarks')->middleware('can:read-remark');
        Route::delete('/{id}', 'destroy')->middleware('can:delete-remark');
        Route::put('/', 'update')->middleware('can:update-remark');
    });

    Route::prefix('/comments')->controller(CommentController::class)->group(function () {
        Route::post('/', 'store')->middleware('can:create-comment');
        Route::post('/reply', 'addReplay')->middleware('can:create-comment');
        Route::get('/project/{project_id}', 'readByProjectID')->middleware('can:read-comment');
        Route::put('/{id}', 'update')->middleware('can:update-comment');
        Route::delete('/{id}', 'delete')->middleware('can:delete-comment');
    });

    Route::prefix('/tasks')->controller(TaskController::class)->group(function () {
        //Route::get('/', 'index');
        Route::get('/project/{project_id}', 'readByProject')->middleware('can:read-task');
        Route::get('/{id}', 'show')->middleware('can:read-task');
        Route::post('/{project_id}', 'store')->middleware('can:create-task');
        Route::put('/{task_id}', 'update')->middleware('can:update-task');
        Route::delete('/{task_id}', 'destroy')->middleware('can:delete-task');
        Route::put('/{task_id}/validate', 'validateTask')->middleware('can:validate-task');
        Route::post('/{task_id}/submit', 'submit')->middleware('can:submit-task');
    });

    Route::prefix('/announcements')->controller(AnnouncementController::class)->group(function () {
        Route::get('/private', 'index_establishment')->middleware('can:read-announcement');
        Route::get('/{id}', 'show')->middleware('can:read-announcement');
        Route::post('/', 'store')->middleware('can:create-announcement');
        Route::put('/{id}', 'update')->middleware('can:update-announcement');
        Route::delete('/{ann_id}', 'destroy')->middleware('can:delete-announcement');
    });

    Route::prefix('/defences')->controller(DefenceController::class)->group(function () 
    {
        Route::get('/', 'index')->middleware('can:read-defence');
        Route::get('/{id}', 'show')->middleware('can:read-defence');
        Route::post('/{project_id}', 'store')->middleware('can:create-defence');
        Route::delete('/{id}', 'destroy')->middleware('can:delete-defence');
        Route::put('/{id}', 'update')->middleware('can:update-defence');
        Route::post('/{id}/files', 'update_files')->middleware('can:upload-files-defence');
    });

    Route::prefix('/periods')->controller(PeriodController::class)->group(function () {
        Route::get('/', 'index_establishment')->middleware('can:read-period');
        Route::put('/', 'update')->middleware('can:update-period');
    });
    Route::prefix('/deliberations')->controller(DeliberationController::class)->group(function(){
        Route::get('/{defence_id}', 'show')->middleware('can:read-deliberation');
        Route::post('/{defence_id}', 'store');
    });

    Route::get('/rooms', function () {
        return [
            'success' => true,
            'message' => 'rooms retreived',
            'data' => [
                'rooms' => auth()->user()->person->establishment->rooms
            ]
        ];
    });

    Route::prefix('/dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('/admin', 'admin')->middleware('can:read-dashboard-admin');
        Route::get('/student', 'student')->middleware('can:read-dashboard-student');
        Route::get('/teacher', 'teacher')->middleware('can:read-dashboard-teacher');
        Route::get('/other', 'other')->middleware('can:read-dashboard-other');
    });
});


Route::prefix('/establishments')->controller(EstablishmentController::class)->group(function () {
    Route::get('/', 'index');
});

Route::prefix('/grades')->controller(GradeController::class)->group(function () {
    Route::get('/', 'index');
});

Route::prefix('/filieres')->controller(FiliereController::class)->group(function () {
    Route::get('/', 'index');
});

Route::prefix('/specialities')->controller(SpecialityController::class)->group(function () {
    Route::get('/', 'index');
});