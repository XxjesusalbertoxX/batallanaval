<template>
  <div class="text-center mb-6">
    <p class="text-[#00FF00] text-sm mb-1">{{ title || 'CÓDIGO DE INVITACIÓN:' }}</p>
    <div class="bg-black border-2 border-[#00FF00] inline-block px-4 py-2 rounded">
      <span class="text-xl font-mono text-[#00FF00] tracking-widest animate-pulse">{{ code }}</span>
    </div>
    <p class="text-[#00FF00] text-xs mt-1">{{ subtitle || 'COMPARTE ESTE CÓDIGO PARA QUE OTROS SE UNAN' }}</p>
  </div>
</template>

<script>
export default {
  name: 'CodeSpan',
  props: {
    code: {
      type: String,
      required: true
    },
    title: {
      type: String,
      default: ''
    },
    subtitle: {
      type: String,
      default: ''
    }
  }
}
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 2s infinite;
}
</style>
