<template>
  <div 
    class="border-4 shadow p-3 overflow-hidden w-full"
    :class="[
      color === 'green' ? 'border-[#00FF00] bg-[#00112b] shadow-[0_0_20px_rgba(0,255,0,0.4)]' :
      color === 'red' ? 'border-[#FF0000] bg-[#120011] shadow-[0_0_15px_rgba(255,0,0,0.3)]' :
      'border-[#00FF00] bg-[#00112b] shadow-[0_0_20px_rgba(0,255,0,0.4)]'
    ]"
    :style="`border-image: repeating-linear-gradient(45deg, 
      ${color === 'green' ? '#00FF00, #00FF00 10px, #007700 10px, #007700' : 
      color === 'red' ? '#FF0000, #FF0000 10px, #770000 10px, #770000' : 
      '#00FF00, #00FF00 10px, #007700 10px, #007700'} 20px) 8;`">
    <!-- Título del panel (opcional) -->
    <div v-if="title" class="text-center mb-4">
      <h2 class="text-2xl mb-1" :class="color === 'green' ? 'text-[#00FF00]' : 'text-[#FF0000]'">{{ title }}</h2>
      <div class="w-full h-1" :class="color === 'green' ? 'bg-[#00FF00]' : 'bg-[#FF0000]'"></div>
    </div>
    
    <!-- Contenido del panel -->
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'GamePanel',
  props: {
    color: {
      type: String,
      default: 'green', // green, red
      validator: (value) => ['green', 'red'].includes(value)
    },
    title: {
      type: String,
      default: ''
    }
  }
}
</script>
