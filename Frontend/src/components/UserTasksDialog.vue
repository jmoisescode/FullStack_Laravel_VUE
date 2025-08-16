<template>
  <div class="p-4">
    <!-- Task Filters -->
    <div class="flex flex-wrap gap-3 mb-4">
      
      <IconField class="flex-1 ">
          <InputIcon class="pi pi-search" />
          <InputText   
            v-model="searchQuery"
            type="text"
            placeholder="Search tasks..."
            class="border w-full h-full border-gray-300 bg-gray-50 rounded px-3 py-2"
            icon="pi pi-search" />
      </IconField>
      <Dropdown
        v-model="filters.status"
        :options="statusOptions"
        optionLabel="name"
        placeholder="Status"
        class="md:w-48"
      />
      <Dropdown
        v-model="filters.priority"
        :options="priorityOptions"
        optionLabel="name"
        placeholder="Priority"
        class=" md:w-48"
      />
    </div>

    <!-- Tasks DataTable -->
    <DataTable
      :value="filteredTasks"
      :paginator="true"
      :rows="10"
      :loading="loading"
      responsiveLayout="scroll"
      class="p-datatable-sm"
    >
      <Column field="title" header="Title">
        <template #body="{ data }">
          <div :class="{ 'line-through': data.status === 'completed' }">
            {{ data.title }}
          </div>
        </template>
      </Column>

      <Column field="priority" header="Priority">
        <template #body="{ data }">
          <Tag
            :value="data.priority"
            :severity="getPrioritySeverity(data.priority)"
          />
        </template>
      </Column>

      <Column field="status" header="Status">
        <template #body="{ data }">
          <Tag
            :value="data.status"
            :severity="getStatusSeverity(data.status)"
          />
        </template>
      </Column>

      <Column field="created_at" header="Created">
        <template #body="{ data }">
          {{ new Date(data.created_at).toLocaleDateString() }}
        </template>
      </Column>

      <Column header="Actions" :exportable="false" style="min-width: 8rem">
        <template #body="{ data }">
          <div class="flex gap-2">
            <Button
              icon="pi pi-check"
              @click="toggleTaskStatus(data)"
              :severity="data.status === 'completed' ? 'warning' : 'success'"
              rounded
              text
            />
            <Button
              v-if="isAdmin"
              icon="pi pi-trash"
              @click="confirmDelete(data)"
              severity="danger"
              rounded
              text
            />
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
 
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useConfirm } from "primevue/useconfirm";
import axios from "@/axios";
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  selectedUser: {
    type: Object,
    required: true,
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:visible", "task-updated", "task-deleted"]);

const confirm = useConfirm();
const loading = ref(false);
const tasks = ref([]);
const filters = ref({
  search: "",
  status: null,
  priority: null,
});

const statusOptions = [
  { name: "All", code: null },
  { name: "Pending", code: "pending" },
  { name: "Completed", code: "completed" },
];

const priorityOptions = [
  { name: "All", code: null },
  { name: "High", code: "high" },
  { name: "Medium", code: "medium" },
  { name: "Low", code: "low" },
];

const filteredTasks = computed(() => {
  return tasks.value.filter((task) => {
    const matchesSearch =
      !filters.value.search ||
      task.title.toLowerCase().includes(filters.value.search.toLowerCase());
    const matchesStatus =
      !filters.value.status?.code || task.status === filters.value.status.code;
    const matchesPriority =
      !filters.value.priority?.code ||
      task.priority === filters.value.priority.code;

    return matchesSearch && matchesStatus && matchesPriority;
  });
});

const fetchUserTasks = async () => {
  if (!props.selectedUser?.id) return;

  loading.value = true;
  try {
    const response = await axios.get(
      `/admin/users/${props.selectedUser.id}/tasks`
    );
    tasks.value = response.data;
  } catch (error) {
    console.error("Error fetching user tasks:", error);
  } finally {
    loading.value = false;
  }
};

const toggleTaskStatus = async (task) => {
  try {
    const newStatus = task.status === "completed" ? "pending" : "completed";
    await axios.put(`/tasks/${task.id}`, {
      ...task,
      status: newStatus,
    });
    task.status = newStatus;
    emit("task-updated", task);
  } catch (error) {
    console.error("Error updating task:", error);
  }
};

const confirmDelete = (task) => {
  confirm.require({
    message: "Are you sure you want to delete this task?",
    header: "Delete Confirmation",
    icon: "pi pi-exclamation-triangle",
    accept: () => deleteTask(task),
  });
};

const deleteTask = async (task) => {
  try {
    await axios.delete(`/tasks/${task.id}`);
    tasks.value = tasks.value.filter((t) => t.id !== task.id);
    emit("task-deleted", task);
  } catch (error) {
    console.error("Error deleting task:", error);
  }
};

const getPrioritySeverity = (priority) => {
  const severities = {
    low: "success",
    medium: "warning",
    high: "danger",
  };
  return severities[priority] || "info";
};

const getStatusSeverity = (status) => {
  return status === "completed" ? "success" : "warning";
};
 

watch(  () => props.selectedUser,
  (newValue, oldValue) => {
    console.log('test')
    if (props.visible) {
      fetchUserTasks();
    }
  },{ immediate: true }
);
</script>
