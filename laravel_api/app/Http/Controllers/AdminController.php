<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class AdminController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function userStatistics(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $dateRange = $request->query('date_range');

        $users = User::with(['tasks' => function ($query) use ($status, $priority, $dateRange) {
            if ($status) {
                $query->where('status', $status);
            }
            if ($priority) {
                $query->where('priority', $priority);
            }
            if ($dateRange) {
                $dates = json_decode($dateRange);
                if (count($dates) === 2) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($dates[0])->startOfDay(),
                        Carbon::parse($dates[1])->endOfDay()
                    ]);
                }
            }
        }]);

        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('tasks', function ($query) use ($search) {
                          $query->where('title', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                      });
            });
        }

        $result = $users->paginate($perPage)
            ->through(function ($user) {
                $tasks = $user->tasks;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tasks' => $tasks,
                    'taskStats' => [
                        'total' => $tasks->count(),
                        'completed' => $tasks->where('status', 'completed')->count(),
                        'pending' => $tasks->where('status', 'pending')->count(),
                        'highPriority' => $tasks->where('priority', 'high')->count(),
                    ]
                ];
            });

        return response()->json($result);
    }
    public function userTasks($userId)
    {
        $user = User::findOrFail($userId);
        $tasks = $user->tasks()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }
}
