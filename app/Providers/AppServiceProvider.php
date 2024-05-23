<?php

namespace App\Providers;

use App\Http\Interfaces\CommentInterface;
use App\Http\Interfaces\EstablishmentInterface;
use App\Http\Interfaces\FiliereInterface;
use App\Http\Interfaces\GradeInterface;
use App\Http\Interfaces\PeriodInterface;
use App\Http\Interfaces\PermissionInterface;
use App\Http\Interfaces\ProjectInterface;
use App\Http\Interfaces\RoleInterface;
use App\Http\Interfaces\UserInterface;
use App\Http\Interfaces\RegisterInterface;
use App\Http\Interfaces\RemarkInterface;
use App\Http\Interfaces\SpecialityInterface;
use App\Http\Interfaces\TaskInterface;
use App\Http\Interfaces\AnnouncementInterface;
use App\Http\Interfaces\DefenceInterface;
use App\Http\Interfaces\DeliberationInterface;
use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\EstablishmentRepository;
use App\Http\Repositories\FiliereRepository;
use App\Http\Repositories\GradeRepository;
use App\Http\Repositories\PeriodRepository;
use App\Http\Repositories\PermissionRepository;
use App\Http\Repositories\ProjectRepository;
use App\Http\Repositories\RoleRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\RegisterRepository;
use App\Http\Repositories\RemarkRepository;
use App\Http\Repositories\SpecialityRepository;
use App\Http\Repositories\TaskRepository;
use App\Http\Repositories\AnnouncementRepository;
use App\Http\Repositories\DefenceRepository;
use App\Http\Repositories\DeliberationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(ProjectInterface::class, ProjectRepository::class);
        $this->app->bind(RegisterInterface::class, RegisterRepository::class);
        $this->app->bind(RemarkInterface::class, RemarkRepository::class);
        $this->app->bind(PeriodInterface::class, PeriodRepository::class);
        $this->app->bind(TaskInterface::class, TaskRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(EstablishmentInterface::class, EstablishmentRepository::class);
        $this->app->bind(GradeInterface::class, GradeRepository::class);
        $this->app->bind(SpecialityInterface::class, SpecialityRepository::class);
        $this->app->bind(FiliereInterface::class, FiliereRepository::class);
        $this->app->bind(AnnouncementInterface::class, AnnouncementRepository::class);
        $this->app->bind(DefenceInterface::class, DefenceRepository::class);
        $this->app->bind(DeliberationInterface::class, DeliberationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
