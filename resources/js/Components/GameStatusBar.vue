<template>
  <div class="flex justify-between items-center">
    <div :class="textColor">
      <slot name="left">
        <h1 class="text-2xl tracking-wider" :style="`text-shadow: 2px 2px 0px #00334d;`">{{ title }}</h1>
      </slot>
    </div>
    
    <div :class="statusColor" class="animate-blink">
      <slot name="center">
        {{ status }}
      </slot>
    </div>
    
    <div :class="textColor" class="text-sm">
      <slot name="right">
        {{ info }}
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GameStatusBar',
  props: {
    title: {
      type: String,
      default: 'BATALLA NAVAL'
    },
    status: {
      type: String,
      default: ''
    },
    info: {
      type: String,
      default: ''
    },
    textColor: {
      type: String,
      default: 'text-[#00FF00]'
    },
    statusColor: {
      type: String,
      default: 'text-[#FF0000]'
    }
  }
}
</script>

<style scoped>
@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.animate-blink {
  animation: blink 1s infinite;
}
</style>
