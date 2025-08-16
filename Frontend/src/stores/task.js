import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "@/axios";
import { useToast } from "primevue";
import { echo } from "@/plugins/echo";

export const useTaskStore = defineStore("task", () => {
  const TaskList = ref([]);
  const toast = useToast();
  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    total: 0,
  });

  const filters = ref({
    status: "",
    priority: "",
    search: "",
  });

  const fetchTasks = async (page = 1) => {
    try {
      const { status, priority } = filters.value;
      const response = await axios.get("/tasks", {
        params: {
          status,
          priority,
          page,
          per_page: pagination.value.perPage,
        },
      });

      TaskList.value = response.data.data;
      pagination.value = {
        currentPage: response.data.current_page,
        perPage: response.data.per_page,
        total: response.data.total,
      };
    } catch (error) {
      toast.add({
        severity: "error",
        summary: "Error fetching tasks",
        detail: error.message,
        life: 3000,
        group: "app_toast",
      });
    }
    return TaskList.value;
  };

  const destroyTask = async (taskId) => {
    try {
      await axios.delete(`/tasks/${taskId}`);
      TaskList.value = TaskList.value.filter((task) => task.id !== taskId);
    } catch (error) {
      toast.add({
        severity: "error",
        summary: "Error deleting task",
        detail: error.message,
        life: 3000,
        group: "app_toast",
      });
    }
  };

  const createTask = async (taskData) => {
    try {
      const response = await axios.post("/tasks", taskData);
      TaskList.value.push(response.data);
    } catch (error) {
      toast.add({
        severity: "error",
        summary: "Error creating task",
        detail: error.message,
        life: 3000,
        group: "app_toast",
      });
    }
  };

  const modifyTask = async (taskId, taskData) => {
    try {
      const response = await axios.put(`/tasks/${taskId}`, taskData);
      const index = TaskList.value.findIndex((task) => task.id === taskId);
      if (index !== -1) {
        TaskList.value[index] = response.data;
      }
    } catch (error) {
      toast.add({
        severity: "error",
        summary: "Error modifying task",
        detail: error.message,
        life: 3000,
        group: "app_toast",
      });
    }
  };

  const reorderTasks = async (newOrder) => {
    try {
      await axios.patch("/tasks/reorder", {
        order: newOrder.map((t) => t.id),
      });
      TaskList.value = newOrder;
    } catch (error) {
      toast.add({
        severity: "error",
        summary: "Error reordering tasks",
        detail: error.message,
        life: 3000,
        group: "app_toast",
      });
    }
  };
 
  const listenForUpdates = () => {
    echo.channel("tasks")
      .listen("TaskUpdated", (e) => {
        console.log("Task updated:", e);
        const idx = TaskList.value.findIndex((t) => t.id === e.task.id);
        if (idx !== -1) TaskList.value[idx] = e.task;
      })
      .listen("TaskCreated", (e) => {
        console.log("Task created:", e);
        TaskList.value.push(e.task);
      })
      .listen("TaskDeleted", (e) => {
        console.log("Task deleted:", e);
        TaskList.value = TaskList.value.filter((t) => t.id !== e.taskId);
      });
  };

  return {
    TaskList,
    pagination,
    filters,
    fetchTasks,
    destroyTask,
    createTask,
    modifyTask,
    reorderTasks,
    listenForUpdates,  
  };
});