<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Audit;
use App\Module;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'audit')->first();
    }

    public function index(User $user = null)
    {
        $this->authorize('allows', [$this->module, 'view']);

        $audits =  Audit::where(function(Builder $query) use ($user) {
            if ($user != null)
                $query->where('user_id', $user->id);
        })->latest();

        $results = $audits->get()->map(function ($audit) {
            $audit['old_values'] = json_decode($audit['old_values']);
            $audit['new_values'] = json_decode($audit['new_values']);

            return $audit;
        });

        $page = Paginator::resolveCurrentPage();
        $perPage = (int) request('per_page', 10);
        $offset = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            array_slice($results->toArray(), $offset, $perPage),
            $results->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath()
            ]);
    }
}
