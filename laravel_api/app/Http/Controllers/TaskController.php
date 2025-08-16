<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\ReorderTasksRequest;
use App\Events\TaskUpdated;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="API Endpoints for Task Management"
 * )
 */
class TaskController extends Controller
{
    use AuthorizesRequests;
    /**
         * @OA\Get(
         *     path="/api/tasks",
         *     summary="Get all tasks for authenticated user",
         *     tags={"Tasks"},
         *     security={{"bearerAuth":{}}},
         *     @OA\Response(
         *         response=200,
         *         description="List of tasks",
         *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Task"))
         *     )
         * )
         */
    public function index(Request $request)
    {
        $status   = $request->query('status');
        $priority = $request->query('priority');
        $perPage = $request->query('per_page', 10); // Default 10 items per page
        $user = auth()->user(); // Get the authenticated user

        $cacheKey = "tasks_{$user->id}_{$status}_{$priority}_{$perPage}_" . $request->query('page', 1);

        $tasks = Cache::remember($cacheKey, 60, function () use ($status, $priority, $user, $perPage) {
            $query = Task::query();

            // Check role through user object

                $query->where('user_id', $user->id);


            return $query->status($status)
                ->priority($priority)
                ->orderBy('order')
                ->with('user:id,name') // Optional: Include user details
                ->paginate($perPage);
        });

        return response()->json($tasks);
    }
    public function adminTasks(Request $request)
    {
        $status   = $request->query('status');
        $priority = $request->query('priority');
        $perPage = $request->query('per_page', 10); // Default 10 items per page

        $cacheKey = "tasks_{$user->id}_{$status}_{$priority}_{$perPage}_" . $request->query('page', 1);

        $tasks = Cache::remember($cacheKey, 60, function () use ($status, $priority, $user, $perPage) {
            $query = Task::query();


            return $query->status($status)
                ->priority($priority)
                ->orderBy('order')
                ->with('user:id,name') // Optional: Include user details
                ->paginate($perPage);
        });

        return response()->json($tasks);
    }

     /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     )
     * )
     */
    public function store(StoreTaskRequest $request)
    {
        return Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'priority' => $request->priority ?? 1
        ]);
    }

    public function reorder(ReorderTasksRequest $request)
    {
            $user = auth()->user(); // Get the authenticated user

        $ids = $request->validated()['order'];
          $query = Task::query();

            // Check role through user object
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
        $ownedIds = $query->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        if (count($ownedIds) !== count($ids)) {
            return response()->json(['message' => 'Invalid task list'], 422);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $taskId) {
                Task::where('id', $taskId)
                    ->where('user_id', auth()->id())
                    ->update(['order' => $index]); // 0,1,2,...
            }
        });
 // broadcast(new \App\Events\TasksReordered(auth()->id(), $ids))->toOthers();
        return response()->noContent();
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->only('title', 'description', 'status', 'priority', 'order'));
      //   broadcast(new TaskUpdated($task))->toOthers();
        return $task;
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
