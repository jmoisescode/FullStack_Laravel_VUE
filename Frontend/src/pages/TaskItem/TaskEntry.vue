<template>
    <div>
    
    <form @submit.prevent="saveTask()" class="space-y-3">
    <div class="flex flex-col gap-2 mb-8">
            <label for="title" class="font-semibold w-24">Title</label>
            <InputText id="title" class="flex-auto"  v-model="form.title"  autocomplete="off" />
        </div>
        <div class="flex flex-col gap-2 mb-8">
            <label for="description" class="font-semibold w-24">Description</label>
            <InputText id="description" class="flex-auto"  v-model="form.description"   autocomplete="off" />
        </div>
        
        <div class="flex flex-col gap-2 mb-8">
            <label for="priority" class="font-semibold  ">Priority</label>
            <Select v-model="selectedPriority" :options="priorityOptions" optionLabel="name" placeholder="Select a Priority" 
            @change="form.priority = selectedPriority.code" >
                <template #value="slotProps">
                    <div v-if="slotProps.value" class="flex items-center">
 
                        <div class="flex-1 font-bold w-80 p-1 px-4 rounded-md" :class="slotProps.value.class" >{{ slotProps.value.name }}</div>
                        
                    </div>
                    <span v-else>
                        {{ slotProps.placeholder }}
                    </span>
                </template>
                <template #option="slotProps">
                    <div class="flex flex-wrap items-center "> 
                        <div class="flex-1 font-bold w-73 p-1 px-4 rounded-md" :class="slotProps.option.class" >{{ slotProps.option.name }}</div>
                    </div>
                </template> 
            </Select>
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="closeform"></Button>
            <Button type="submit" label="Save" :loading="isSubmit"></Button>
        </div>
        </form>
    </div>
</template>

<script setup>
import { ref, watch,reactive } from 'vue'  
import { useTaskStore } from "../../stores/task";

import { useToast } from "primevue/usetoast"; 
 

const toast = useToast()
const taskStore = useTaskStore();
const form = reactive({
  title: '',
  description: '',
  status: 'pending',
  priority: 'low'
});

const selectedPriority = ref(null);
const selectedStatus = ref(null);
const props =defineProps({
    taskForm: {
        type: Object,
        required: true
    }
})

const isSubmit = ref(false)
const emit = defineEmits(['update:taskForm', 'close'])

const priorityOptions = ref([
    { name: 'Low', code: 'low' , severity: 'success' ,class:'bg-green-100 text-green-700'},
    { name: 'Medium', code: 'medium' , severity: 'warn' ,class:'bg-orange-100 text-orange-700'},
    { name: 'High', code: 'high' , severity: 'danger' ,class:'bg-red-100 text-red-700'}
])

const validateForm = () => {
    if (!form.title) {
        return 'Title is required';
    }
    if (!form.description) {
        return 'Description is required';
    }
    if (!form.priority) {
        return 'Priority is required';
    }
    return null;
    
}
const saveTask = async()=>{ 
    try{ 
    const error = validateForm() 
    if (isSubmit.value) return;
    isSubmit.value = true;
    if (error) {
        console.error(error)
        return
    }
    if (form.id) { 
        await taskStore.modifyTask(form.id, form);
          toast.add({ severity: 'success', summary: 'Success', detail: 'Task updated successfully!', life: 3000 ,
              group: "app_toast",});
    } else { 
        await taskStore.createTask({ ...form });
          toast.add({ severity: 'success', summary: 'Success', detail: 'Task created successfully!', life: 3000 ,
              group: "app_toast",});
    } 
    isSubmit.value = false;
    closeform()
    }
    catch(error){
         toast.add({ severity: 'error', summary: 'Error logging in', detail: error.message, life: 3000 ,
              group: "app_toast",});
    }
}

const closeform = ()=>{
    isSubmit.value = false;
  emit('close-form' );
}
watch(() => props.taskForm, (newVal) => { 
      Object.assign(form, newVal); 
    selectedPriority.value = priorityOptions.value.find(option => option.code === newVal.priority);
    // selectedStatus.value = statusOptions.value.find(option => option.code === newVal.status);
}, { immediate: true })
</script>

<style  scoped>
.p-select-option {
 padding:0px !important
}
</style>