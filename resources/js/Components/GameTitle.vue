<template>
  <h1 
    class="text-center text-4xl text-[#00FF00] mb-6 tracking-widest animate-pulse" 
    :class="[sizeClass]"
    :style="`text-shadow: ${shadowSize}px ${shadowSize}px 0px #00334d, -${smallShadow}px -${smallShadow}px 0px #00FFFF;`"
  >
    {{ title }}
  </h1>
</template>

<script>
export default {
  name: 'GameTitle',
  props: {
    title: {
      type: String,
      default: 'BATALLA NAVAL'
    },
    size: {
      type: String,
      default: 'lg' // sm, md, lg, xl
    }
  },
  computed: {
    sizeClass() {
      switch(this.size) {
        case 'sm': return 'text-xl';
        case 'md': return 'text-2xl';
        case 'lg': return 'text-4xl';
        case 'xl': return 'text-5xl';
        default: return 'text-4xl';
      }
    },
    shadowSize() {
      return this.size === 'xl' ? 5 : this.size === 'lg' ? 4 : this.size === 'md' ? 3 : 2;
    },
    smallShadow() {
      return this.size === 'xl' ? 2 : this.size === 'lg' ? 2 : this.size === 'md' ? 1 : 1;
    }
  }
}
</script>

<style scoped>
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.animate-pulse {
  animation: pulse 2s infinite;
}
</style>
