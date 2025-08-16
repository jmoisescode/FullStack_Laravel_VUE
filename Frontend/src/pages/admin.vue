<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6 text-gray-600">
    <!-- Filters Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="text-sm text-gray-600 mb-1 block">Search</label>
          <InputText 
            v-model="filters.search" 
            placeholder="Search users or tasks..."
            class="w-full"
          />
        </div>
        
        <div>
          <label class="text-sm text-gray-600 mb-1 block">Status</label>
          <Dropdown
            v-model="filters.status"
            :options="[
              { label: 'All', value: null },
              { label: 'Completed', value: 'completed' },
              { label: 'Pending', value: 'pending' }
            ]"
            optionLabel="label"
            optionValue="value"
            placeholder="Select Status"
            class="w-full"
          />
        </div>
        
        <div>
          <label class="text-sm text-gray-600 mb-1 block">Priority</label>
          <Dropdown
            v-model="filters.priority"
            :options="[
              { label: 'All', value: null },
              { label: 'High', value: 'high' },
              { label: 'Medium', value: 'medium' },
              { label: 'Low', value: 'low' }
            ]"
            optionLabel="label"
            optionValue="value"
            placeholder="Select Priority"
            class="w-full"
          />
        </div>
        
        <div>
          <label class="text-sm text-gray-600 mb-1 block">Date Range</label>
          <Calendar 
            v-model="filters.dateRange" 
            selectionMode="range" 
            :showIcon="true"
            class="w-full"
          />
        </div>
      </div>
    </div>

 
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div v-for="stat in statistics" :key="stat.title" 
           class="bg-white rounded-lg shadow-sm p-4 transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">{{ stat.title }}</p>
            <h3 class="text-2xl font-bold mt-1">{{ stat.value }}</h3>
            <p class="text-xs text-gray-400 mt-1">
              <span :class="stat.trend >= 0 ? 'text-green-500' : 'text-red-500'">
                {{ stat.trend > 0 ? '↑' : stat.trend < 0 ? '↓' : '–' }} 
                {{ Math.abs(stat.trend) }}
              </span>
              <span class="ml-1">vs previous</span>
            </p>
          </div>
          <div :class="`bg-${stat.color}-100 p-3 rounded-full`">
            <i :class="`pi ${stat.icon} text-${stat.color}-600 text-xl`"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- User List -->
    <div class="bg-white rounded-lg shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4">
        <div v-for="user in userStats.data" :key="user.id" 
             class="bg-white border-2 rounded-lg p-4 border-gray-300 hover:shadow-md transition-all" @click="handleUserCardClick(user)" >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h3 class="font-semibold">{{ user.name }}</h3>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
              </div>
            </div>
             
          </div>

          <!-- Task Progress -->
          <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
              <span>Tasks Progress</span>
              <span class="font-semibold">
                {{ Math.round((user.taskStats.completed / user.taskStats.total) * 100) }}%
              </span>
            </div>
            <ProgressBar 
              :value="(user.taskStats.completed / user.taskStats.total) * 100" 
              class="h-2"
            />
            
            <!-- Task Statistics -->
            <div class="grid grid-cols-3 gap-2 mt-4">
              <div class="text-center p-2 bg-gray-50 rounded">
                <div class="text-sm font-semibold">{{ user.taskStats.total }}</div>
                <div class="text-xs text-gray-500">Total</div>
              </div>
              <div class="text-center p-2 bg-green-50 rounded">
                <div class="text-sm font-semibold text-green-600">
                  {{ user.taskStats.completed }}
                </div>
                <div class="text-xs text-gray-500">Done</div>
              </div>
              <div class="text-center p-2 bg-orange-50 rounded">
                <div class="text-sm font-semibold text-orange-600">
                  {{ user.taskStats.pending }}
                </div>
                <div class="text-xs text-gray-500">Pending</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t"     v-if="userStats.total > 10">
        <Paginator 
          v-model:first="first" 
          :rows="10" 
          :totalRecords="userStats.total"
          template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
          class="p-paginator-sm"
          @page="onPageChange($event)" 
      
        />
      </div>
    </div>

    <!-- User Tasks Dialog - New Component -->
      <Dialog 
    v-model:visible="showTasksDialog"
    :header="selectedUser ? `${selectedUser.name}'s Tasks` : 'User Tasks'"
    :modal="true"
    class="w-full md:w-3/4 lg:w-2/3"
  >
    <UserTasksDialog 
      :selected-user="selectedUser"
      :is-admin="true"
      :visible="showTasksDialog"
      @task-updated="onTaskUpdated"
      @task-deleted="onTaskDeleted"
    />
    </Dialog>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from '@/axios';
import UserTasksDialog from '@/components/UserTasksDialog.vue';
import { useTaskStore } from '../stores/task';
const taskStore = useTaskStore();
const filters = ref({
  search: '',
  status: null,
  priority: null,
  dateRange: null
});

// Create a debounced search
const searchTimeout = ref(null);
watch(() => filters.value.search, (newValue) => {
  if (searchTimeout.value) clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    fetchUserStats();
  }, 300);
});

// Watch all filters
watch([() => filters.value.status, () => filters.value.priority, () => filters.value.dateRange], 
  () => {
    fetchUserStats();
  }
);

const searchQuery = ref('');
const selectedFilter = ref(null);
const first = ref(0);
const userStats = ref({
  data: [],
  total: 0
});

const tasks = ref([]);

const filterOptions = [
  { name: 'All Users', code: 'all' },
  { name: 'Active Users', code: 'active' },
  { name: 'Inactive Users', code: 'inactive' }
];

const statistics = computed(() => {
  const allTasks = userStats.value.data.reduce((acc, user) => {
    return acc.concat(user.tasks || []);
  }, []);

  const total = allTasks.length;
  const completed = allTasks.filter(task => task.status === 'completed').length;
  const pending = allTasks.filter(task => task.status === 'pending').length;
  const highPriority = allTasks.filter(task => task.priority === 'high').length;

  return [
    {
      title: 'Total Tasks',
      value: total.toString(),
      trend: 0, // You can calculate trend if you store historical data
      icon: 'pi-list',
      color: 'blue'
    },
    {
      title: 'Completed Tasks',
      value: `${Math.round((completed / total) * 100)}%`,
      trend: completed - pending,
      icon: 'pi-check-circle',
      color: 'green'
    },
    {
      title: 'Pending Tasks',
      value: pending.toString(),
      trend: pending - completed,
      icon: 'pi-clock',
      color: 'orange'
    },
    {
      title: 'High Priority',
      value: `${Math.round((highPriority / total) * 100)}%`,
      trend: highPriority,
      icon: 'pi-exclamation-circle',
      color: 'red'
    }
  ];
});

const fetchUserStats = async (page = 1) => {
  try {
    const response = await axios.get('/admin/user-statistics', {
      params: {
        page,
        per_page: 10,
        search: filters.value.search,
        status: filters.value.status,
        priority: filters.value.priority,
        date_range: filters.value.dateRange,
      }
    });
    userStats.value = response.data;
  } catch (error) {
    console.error('Error fetching user statistics:', error);
  }
};

const onPageChange = async (event) => {
  const page = Math.floor(event.first / event.rows) + 1;
  await fetchUserStats(page);
};

const showUserMenu = (user) => {
  // Implement user menu functionality
};

// New refs for user tasks dialog
const showTasksDialog = ref(false);
const selectedUser = ref(null);

// New methods for handling user tasks
const showUserTasks = (user) => { 
  selectedUser.value = user;
  showTasksDialog.value = true;
};

const onTaskUpdated = (task) => {
  // Refresh statistics or update local data
  fetchUserStats();
};

const onTaskDeleted = (task) => {
  // Refresh statistics or update local data
  fetchUserStats();
};

// Update your user card click handler
const handleUserCardClick = (user) => {
  showUserTasks(user);
};

onMounted(() => {
  fetchUserStats();  
});
</script>

<style scoped>
.p-paginator {
  background: transparent;
  border: none;
}

.p-progressbar {
  background: #f3f4f6;
}

.p-progressbar .p-progressbar-value {
  background: #3b82f6;
}

.p-button.p-button-text {
  color: #64748b;
}

.p-inputtext-sm {
  font-size: 0.875rem;
  padding: 0.4rem 0.75rem;
}
</style>