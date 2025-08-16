<template>
    <ul class="text-sm mt-2 space-y-1">
      <li v-for="(rule, key) in rules" :key="key" class="flex items-center">
        <i
          :class="[
            'pi',
            rule.valid ? 'pi-check-circle text-green-500' : 'pi-times-circle text-red-500',
            'mr-2',
          ]"
        />
        <span :class="rule.valid ? 'text-green-600' : 'text-red-600'">{{ rule.message }}</span>
      </li>
    </ul>

</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    password: {type : String,required:true},
    confirmPassword: { type: String, required: true },
})

const rules = computed(() => ({
  length: {
    valid: props.password.length >= 8,
    message: 'Password must be at least 8 characters long.',
  },
  uppercase: {
    valid: /[A-Z]/.test(props.password),
    message: 'Password must include at least one uppercase letter.',
  },
  lowercase: {
    valid: /[a-z]/.test(props.password),
    message: 'Password must include at least one lowercase letter.',
  },
  number: {
    valid: /[0-9]/.test(props.password),
    message: 'Password must include at least one number.',
  },
  special: {
    valid: /[^A-Za-z0-9]/.test(props.password),
    message: 'Password must include at least one special character.',
  },
  match : {
    valid : !props.password ? false : props.password === props.confirmPassword,
    message : 'Password and Confirm Password must match.',
  }
}))
</script>

<style lang="scss" scoped>

</style>