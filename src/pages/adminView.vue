<template>
  <div class="p-6 text-gray-700">
    <!-- Overall Statistics -->
    <h2 class="text-2xl font-bold mb-6">Overall Statistics</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <!-- ...existing statistics cards... -->
    </div>

    <!-- User Statistics Table -->
    <div class="bg-white rounded-lg shadow mt-6">
      <div class="p-4 border-b border-gray-200">
        <h3 class="text-xl font-semibold">User Task Statistics</h3>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tasks</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">High Priority</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in userStats.data" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.taskStats.total }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-green-600">
                  {{ user.taskStats.completed }}
                  <span class="text-gray-500 text-xs">({{ user.taskStats.completedPercentage }}%)</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-orange-600">
                  {{ user.taskStats.pending }}
                  <span class="text-gray-500 text-xs">({{ user.taskStats.pendingPercentage }}%)</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-red-600">
                  {{ user.taskStats.highPriority }}
                  <span class="text-gray-500 text-xs">({{ user.taskStats.highPriorityPercentage }}%)</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  @click="viewUserTasks(user.id)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  View Tasks
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t border-gray-200">
        <Paginator
          v-model:first="first"
          :rows="perPage"
          :totalRecords="userStats.total"
          :rowsPerPageOptions="[10, 20, 50]"
          @page="onPageChange($event)"
          class="bg-white"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTaskStore } from "@/stores/task";
import { useRouter } from 'vue-router'; 
import axios from '@/axios';

const router = useRouter();
const taskStore = useTaskStore();

// Pagination state
const first = ref(0);
const perPage = ref(10);
const userStats = ref({
  data: [],
  total: 0
});

// Fetch user statistics with pagination
const fetchUserStats = async (page = 1) => {
  try {
    const response = await axios.get('/admin/user-statistics', {
      params: {
        page,
        per_page: perPage.value
      }
    });
    userStats.value = response.data;
  } catch (error) {
    console.error('Error fetching user statistics:', error);
  }
};

// Navigation
const viewUserTasks = (userId) => {
  router.push(`/admin/users/${userId}/tasks`);
};

// Pagination handlers
const onPageChange = async (event) => {
  const page = Math.floor(event.first / event.rows) + 1;
  await fetchUserStats(page);
};

onMounted(async () => {
  await fetchUserStats();
});
</script>