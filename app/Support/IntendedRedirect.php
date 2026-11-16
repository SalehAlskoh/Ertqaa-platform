<?php

namespace App\Support;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IntendedRedirect
{
    public function resolve(Request $request, mixed $intended): ?string
    {
        if (! is_string($intended) || ! $request->user()) {
            return null;
        }

        $decoded = rawurldecode($intended);
        $safeCharacters = ! preg_match('/[\x00-\x1F\x7F\\\\]/', $decoded);
        $localPath = str_starts_with($intended, '/') && ! str_starts_with($decoded, '//');
        $sameOrigin = str_starts_with($intended, $request->getSchemeAndHttpHost().'/');
        if (! $safeCharacters || (! $localPath && ! $sameOrigin)) {
            return null;
        }

        try {
            // Match and bind a GET without executing its middleware or controller.
            $route = Route::getRoutes()->match(Request::create($intended, 'GET'));
            Route::substituteBindings($route);
            Route::substituteImplicitBindings($route);
            $gate = Gate::forUser($request->user());
            $hasGate = false;
            foreach ($route->gatherMiddleware() as $middleware) {
                if (str_starts_with($middleware, 'can:')) {
                    $hasGate = true;
                    [$ability, $parameters] = array_pad(explode(',', substr($middleware, 4), 2), 2, null);
                    $arguments = $parameters ? array_map(fn ($key) => $route->parameter($key), explode(',', $parameters)) : [];
                    if (! $gate->allows($ability, $arguments)) {
                        return null;
                    }
                }
            }

            $name = $route->getName();
            $group = $route->parameter('trainingGroup') ?? $route->parameter('training_group');
            $allowed = match ($name) {
                'admin.dashboard' => $gate->allows('dashboard-admin'),
                'staff.dashboard' => $gate->allows('dashboard-staff'),
                'instructor.dashboard' => $gate->allows('dashboard-instructor'),
                'student.dashboard' => $gate->allows('dashboard-student'),
                'lectures.show' => $gate->allows('view', $route->parameter('lecture')),
                'student.applications.show' => $route->parameter('application')->student_user_id === $request->user()->id
                    && $gate->allows('view', $route->parameter('application')),
                'admin.applications.show' => $gate->allows('view', $route->parameter('application')),
                'admin.applications.confirm' => $gate->allows('confirm', $route->parameter('application')),
                'student.enrollments.show' => $route->parameter('enrollment')->application->student_user_id === $request->user()->id
                    && $gate->allows('view', $route->parameter('enrollment')),
                'instructor.groups.show', 'admin.training-groups.show' => $gate->allows('view', $group),
                'admin.training-groups.students' => $gate->allows('viewStudents', $group),
                'instructor.groups.students' => $group->instructor_user_id === $request->user()->id
                    && $gate->allows('viewStudents', $group),
                'courses.show' => (bool) $route->parameter('course')->is_published,
                'groups.show' => $route->parameter('trainingGroup')->status === 'published' && $route->parameter('trainingGroup')->course->is_published,
                'home', 'courses.index', 'groups.index', 'profile.edit', 'notifications.index', 'dashboard' => true,
                // Other current management/list pages are protected by their existing route Gates.
                default => $hasGate && is_string($name) && (str_starts_with($name, 'admin.') || str_starts_with($name, 'instructor.') || str_starts_with($name, 'student.')),
            };

            return $allowed ? $intended : null;
        } catch (HttpException|ModelNotFoundException|InvalidArgumentException) {
            return null;
        }
    }
}
