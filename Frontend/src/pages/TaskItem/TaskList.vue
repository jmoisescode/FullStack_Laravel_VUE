<template>
  <div class="p-6 max-w-[1200px] mx-auto text-black">
    <h1 class="text-2xl font-bold mb-6">Task Management</h1>


    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row md:flex-wrap gap-3 mb-4">
      <Select
        v-model="statusFilter"
        :options="statusOptions"
        optionLabel="name"
        placeholder="Select a Status"
        @change="filterTasked"
      >
        <template #value="slotProps">
          <div v-if="slotProps.value" class="flex items-center">
            <div
              class="flex-1 font-bold w-40 p-1 px-4 rounded-md"
              :class="slotProps.value.class"
            >
              {{ slotProps.value.name }}
            </div>
          </div>
          <span v-else>
            {{ slotProps.placeholder }}
          </span>
        </template>
        <template #option="slotProps">
          <div class="flex flex-wrap items-center">
            <div
              class="flex-1 font-bold w-40 p-1 px-4 rounded-md"
              :class="slotProps.option.class"
            >
              {{ slotProps.option.name }}
            </div>
          </div>
        </template>
      </Select>
      <Select
        v-model="priorityFilter"
        :options="priorityOptions"
        optionLabel="name"
        placeholder="Select a Priority"
        @change="filterTasks"
      >
        <template #value="slotProps">
          <div v-if="slotProps.value" class="flex items-center">
            <div
              class="flex-1 font-bold w-40 p-1 px-4 rounded-md"
              :class="slotProps.value.class"
            >
              {{ slotProps.value.name }}
            </div>
          </div>
          <span v-else>
            {{ slotProps.placeholder }}
          </span>
        </template>
        <template #option="slotProps">
          <div class="flex flex-wrap items-center">
            <div
              class="flex-1 font-bold w-40 p-1 px-4 rounded-md"
              :class="slotProps.option.class"
            >
              {{ slotProps.option.name }}
            </div>
          </div>
        </template>
      </Select>
      <IconField class="flex-1 ">
          <InputIcon class="pi pi-search" />
          <InputText   
            v-model="searchQuery"
            type="text"
            placeholder="Search tasks..."
            class="border w-full h-full border-gray-300 bg-gray-50 rounded px-3 py-2"
            icon="pi pi-search" />
      </IconField>
       
      <Button
        @click="openNewTask"
        severity="info"
      >
        + Add Task
      </Button>
    </div> 
    <draggable v-model="tasks" @end="saveOrder" item-key="id" class="space-y-3">
      <template #item="{ element: task }">
        <div
          class="task-item p-4 border border-gray-200 rounded shadow-md flex-col gap-3 sm:flex-row flex justify-between sm:items-center transition-all duration-500 ease-in-out"
          :class="{
            'task-completed': task.status === 'completed',
            'task-pending': task.status === 'pending',
          }"
        >
          <div>
            <h2 class="font-semibold text-lg flex items-center">
              <span
                :class="{
                  'bg-green-100 text-green-700': task.priority === 'low',
                  'bg-orange-100 text-orange-700': task.priority === 'medium',
                  'bg-red-100 text-red-700': task.priority === 'high',
                }"
                class="px-2 py-1 rounded text-sm mr-2"
              >
                {{ task.priority }}
              </span>
              <Transition name="text-fade" mode="out-in">
                <span
                  :key="task.status"
                  :class="{
                    'line-through text-gray-400': task.status === 'completed',
                  }"
                  class="transition-all duration-300"
                >
                  {{ task.title }}
                </span>
              </Transition>
            </h2>
            <p
              class="text-gray-600 transition-all duration-300"
              :class="{ 'opacity-75': task.status === 'completed' }"
            >
              {{ task.description }}
            </p>
          </div>
          <div class="space-x-2 flex-shrink-0 flex justify-end">
            <Button
              :icon="
                task.status === 'completed' ? 'pi pi-refresh' : 'pi pi-check'
              "
              :severity="task.status === 'completed' ? 'help' : 'success'"
              rounded
              variant="outlined"
              aria-label="Cancel"
              @click="markCompleted(task)"
            />

            <Button
              icon="pi pi-pencil"
              severity="info"
              rounded
              variant="outlined"
              aria-label="Cancel"
              v-if="isAdmin"
              @click="openEditTask(task)"
            />

            <Button
              icon="pi pi-trash"
              severity="danger"
              rounded
              variant="outlined"
              aria-label="Cancel"
              v-if="isAdmin" 
              @click="confirmDelete(task.id)"
            />
          </div>
        </div>
      </template>
    </draggable>

    <!-- Add this pagination component -->
    <div class="flex justify-between items-center mt-6">
      <div class="text-sm text-gray-600">
      </div>
      <Paginator
        v-model:first="first"
        :rows="taskStore.pagination.perPage"
        :totalRecords="taskStore.pagination.total"
        :rowsPerPageOptions="[5, 10, 20, 50]"
        @page="onPageChange($event)" 
        v-if="taskStore.pagination.total > 5"
        template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
        class="flex-1 "
      >
        <template #start> 
        Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }} tasks
        </template>
      </Paginator>
    </div>

    <!-- Modal -->
    <Dialog
      v-model:visible="showModal"
      modal
      :header="taskForm.id ? 'Edit Task' : 'New Task'"
      :style="{ width: '25rem' }"
    >
      <TaskEntry :taskForm="taskForm" 
          @close-form="closeForm()"/>
    </Dialog>
  </div>
</template>
<script setup>
import { ref, computed, watch, onBeforeMount } from "vue";
import { useConfirm } from "primevue/useconfirm";
import draggable from "vuedraggable"; 
import TaskEntry from "./TaskEntry.vue";

import { useTaskStore } from "../../stores/task";

const taskStore = useTaskStore();
const confirm = useConfirm();

const isAdmin = true; // change to false to test non-admin

const priorityOptions = ref([
  {
    name: "All Priorities",
    code: "all",
    severity: "success",
    class: "bg-gray-100 text-gray-700",
  },
  {
    name: "Low",
    code: "low",
    severity: "success",
    class: "bg-green-100 text-green-700",
  },
  {
    name: "Medium",
    code: "medium",
    severity: "warn",
    class: "bg-orange-100 text-orange-700",
  },
  {
    name: "High",
    code: "high",
    severity: "danger",
    class: "bg-red-100 text-red-700",
  },
]);
const statusOptions = ref([
  {
    name: "All Statuses",
    code: "all",
    severity: "success",
    class: "bg-gray-100 text-gray-700",
  },
  {
    name: "Pending",
    code: "pending",
    severity: "success",
    class: "bg-orange-100 text-orange-700",
  },
  {
    name: "Completed",
    code: "completed",
    severity: "danger",
    class: "bg-green-100 text-green-700",
  },
]);

const tasks = ref([
 
]);

// Modal state
const showModal = ref(false);
const taskForm = ref({
  id: null,
  title: "",
  description: "",
  priority: "low",
  status: "pending",
});
const isDelete = ref(false)

// Filters
const statusFilter = ref(statusOptions.value[0]);
const priorityFilter = ref(priorityOptions.value[0]);
const searchQuery = ref("");
const first = ref(0);

// Filtered tasks
const filteredTasks = computed(() => {
  return tasks.value.filter((task) => {
    const matchesStatus =
      statusFilter.value === "All" || task.status === statusFilter.value;
    const matchesPriority =
      priorityFilter.value === "All" || task.priority === priorityFilter.value;
    const matchesSearch =
      task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      task.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesStatus && matchesPriority && matchesSearch;
  });
});
const openNewTask = () => {
  taskForm.value = {
    id: null,
    title: "",
    description: "",
    priority: "low",
    status: "pending",
  };
  showModal.value = true;
};
const openEditTask = (task) => {
  taskForm.value = task;
  showModal.value = true;
};
const markCompleted = (task) => {
  task.status = task.status === "completed" ? "pending" : "completed";
}; 

const confirmDelete = (id)=>{
  confirm.require({
        message: 'Are you sure you want to proceed?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Save'
        },
        accept: () => {
            deleteTask(id)
        },
        reject: () => {
            
        }
    });
  
}
const deleteTask = async(id) => { 
  if (isAdmin) {
   const result = await  taskStore.destroyTask(id);
   if (result) {
       tasks.value = tasks.value.filter((t) => t.id !== id);
   }
  }
  await construct();
};

const saveOrder = async() => { 
  await taskStore.reorderTasks(tasks.value);
};
const closeForm = () => {
  showModal.value = false;
  taskForm.value = {
    id: null,
    title: "",
    description: "",
    priority: priorityOptions.value[0],
    status: statusOptions.value[0],
  };
  construct()
};
//Filtering methods
const filterTasks = () => {
  tasks.value = [...originalTasks.value];
  tasks.value = tasks.value.filter((task) => {
    const statusMatch =
      statusFilter.value.code === "all" ||
      task.status === statusFilter.value.code;
    const priorityMatch =
      priorityFilter.value.code === "all" ||
      task.priority === priorityFilter.value.code;
    const searchMatch =
      searchQuery.value === "" ||
      task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      task.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    return statusMatch && priorityMatch && searchMatch;
  });
};

const originalTasks = ref([])
// Rename filterTasked to filterTasks in your template
const filterTasked = filterTasks; // This line ensures backward compatibility

// Optional: Add a watcher for search query
watch(searchQuery, () => {
  filterTasks();
});

// Optional: Keep a backup of original tasks for filtering

// Reset filters method
const resetFilters = () => {
  tasks.value = [...originalTasks.value];
  statusFilter.value = statusOptions.value[0];
  priorityFilter.value = priorityOptions.value[0];
  searchQuery.value = "";
};
const construct = async () => {
  originalTasks.value = [];
  await taskStore.fetchTasks(1);
  tasks.value = taskStore.TaskList;
  originalTasks.value = [...tasks.value];
  if (taskStore.TaskList.length > 0) {
    await taskStore.reorderTasks(taskStore.TaskList);
  }
};

// Add this computed property after your other computed properties
const statistics = computed(() => {
  const total = tasks.value.length;
  const completed = tasks.value.filter(task => task.status === 'completed').length;
  const pending = tasks.value.filter(task => task.status === 'pending').length;
  const highPriority = tasks.value.filter(task => task.priority === 'high').length;

  return {
    total,
    completed,
    pending,
    highPriority,
    completedPercentage: total ? Math.round((completed / total) * 100) : 0,
    pendingPercentage: total ? Math.round((pending / total) * 100) : 0,
    highPriorityPercentage: total ? Math.round((highPriority / total) * 100) : 0
  };
});

// Add these computed properties
const paginationInfo = computed(() => {
  const { currentPage, perPage, total } = taskStore.pagination;
  const from = ((currentPage - 1) * perPage) + 1;
  const to = Math.min(currentPage * perPage, total);
  
  return {
    from,
    to,
    total
  };
});

onBeforeMount(async () => {
  await construct();
});

// Add these methods
const onPageChange = async (event) => {
  const page = Math.floor(event.first / event.rows) + 1;
  await taskStore.fetchTasks(page);
};

const onPerPageChange = async () => {
  first.value = 0; // Reset to first page
  await taskStore.fetchTasks(1);
};
</script>

<style> 
/* Replace the @apply styles with direct classes in the template */
.p-paginator {
  background-color: white;
  border-radius: 0.5rem;
  padding: 0.5rem;
  box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}

.p-paginator .p-paginator-page.p-highlight {
  background-color: #3b82f6;
  color: white;
}

.p-dropdown {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

/* Add these transition styles */
.text-fade-enter-active,
.text-fade-leave-active {
  transition: all 0.5s ease;
}

.text-fade-enter-from,
.text-fade-leave-to {
  opacity: 0;
  transform: translateX(10px);
}

.text-fade-enter-to,
.text-fade-leave-from {
  opacity: 1;
  transform: translateX(0);
}

/* Optional: Add a transition for the task list items */
.fade-move,
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s cubic-bezier(0.55, 0, 0.1, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scaleY(0.01) translate(30px, 0);
}

.fade-leave-active {
  position: absolute;
}

/* Add these new styles for the task completion animation */
.task-item {
  background: white;
  position: relative;
  overflow: hidden;
}

.task-item::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to right, #e2f0e7, #ffffff);
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
  z-index: 0;
}

.task-item > * {
  position: relative;
  z-index: 1;
}

.task-completed::before {
  opacity: 0.7;
}

.task-pending::before {
  opacity: 0;
}

/* Enhance existing transition for smoother effect */
.text-fade-enter-active,
.text-fade-leave-active {
  transition: all 0.5s ease;
}

/* Optional: Add a subtle scale effect */
.task-completed {
  transform: scale(0.99);
}

.task-pending {
  transform: scale(1);
}

/* Add this to your existing styles */
.stats-card {
  transition: all 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1),
    0 2px 4px -2px rgb(0 0 0 / 0.1);
}
</style>
