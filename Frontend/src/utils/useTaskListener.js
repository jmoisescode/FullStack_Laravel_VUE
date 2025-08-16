import { useTaskStore } from "@/stores/taskStore";
import { echo } from "@/plugins/echo";

export function useTaskListeners() {
  const taskStore = useTaskStore();

  const listenForUpdates = () => {
    echo.channel("tasks")
      .listen("TaskUpdated", (e) => {
        console.log("Task updated:", e);
        taskStore.updateTask(e);
      })
      .listen("TaskCreated", (e) => {
        console.log("Task created:", e);
        taskStore.addTask(e);
      })
      .listen("TaskDeleted", (e) => {
        console.log("Task deleted:", e);
        taskStore.deleteTask(e.id);
      });
  };

  return { listenForUpdates };
}