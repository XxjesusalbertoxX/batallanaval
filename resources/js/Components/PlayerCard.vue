<template>
  <div>
    <!-- Avatar del jugador -->
  <img
    :src="stats.avatar_path"
    alt="Avatar animado"
    :class="[
      'mx-auto my-4 w-24 h-24 object-cover rounded-full overflow-hidden',
      isPlayer ? 'bg-[#004400] border-2 border-[#00FF00]' : 'bg-[#440000] border-2 border-[#FF0000] animate-pulse-slow opacity-50'
    ]"
  />

    <!-- <div 
      :class="[
        'mx-auto my-4 w-24 h-24 overflow-hidden relative',
        isPlayer ? 'bg-[#004400] border-2 border-[#00FF00]' : 'bg-[#440000] border-2 border-[#FF0000] animate-pulse-slow opacity-50'
      ]"
    >
      <template v-if="isPlayer">
        <div class="w-10 h-10 absolute top-6 left-7 bg-[#00FF00] rounded-full"></div>
        <div class="w-16 h-8 absolute bottom-2 left-4 bg-[#00FF00] rounded-t-full"></div>
      </template>
      <template v-else>
        <div class="flex justify-center items-center h-full">
          <span class="text-[#FF0000] text-xs text-center">{{ waitingText }}</span>
        </div>
      </template>
    </div> -->

    <!-- Estadísticas del jugador -->
    <div 
      class="space-y-3 mt-6"
      :class="isPlayer ? 'text-[#00FF00]' : 'text-[#FF0000]'"
    >
      <!-- Nombre -->
      <div class="flex justify-between items-center border-b border-dashed pb-1"
           :class="isPlayer ? 'border-[#007700]' : 'border-[#770000]'">
        <span>NOMBRE:</span>
        <slot name="name">
          <span v-if="isPlayer">{{ playerName }}</span>
          <span v-else>???</span>
        </slot>
      </div>
      
      <!-- Victorias -->
      <div class="flex justify-between items-center border-b border-dashed pb-1"
           :class="isPlayer ? 'border-[#007700]' : 'border-[#770000]'">
        <span>VICTORIAS:</span>
        <span>{{ isPlayer ? stats.wins : '???' }}</span>
      </div>
      
      <!-- Derrotas -->
      <div class="flex justify-between items-center border-b border-dashed pb-1"
           :class="isPlayer ? 'border-[#007700]' : 'border-[#770000]'">
        <span>DERROTAS:</span>
        <span>{{ isPlayer ? stats.losses : '???' }}</span>
      </div>
      
      <!-- Precisión -->
      <div class="flex justify-between items-center border-b border-dashed pb-1"
           :class="isPlayer ? 'border-[#007700]' : 'border-[#770000]'">
        <span>PRECISIÓN:</span>
        <span>{{ isPlayer ? stats.precision + '%' : '???' }}</span>
      </div>
      
      <!-- Barcos -->
      <div class="flex justify-between items-center border-b border-dashed pb-1"
           :class="isPlayer ? 'border-[#007700]' : 'border-[#770000]'">
        <span>Nivel:</span>
        <!-- <div class="flex space-x-1">
          <div 
            v-for="i in 5" 
            :key="`ship-${i}`" 
            class="w-3 h-3" 
            :class="isPlayer ? 'bg-[#00FF00]' : 'bg-[#330000]'"
          ></div>
        </div> -->
        <span>{{ isPlayer ? stats.level: '???' }}</span>
      </div>
    </div>
    
    <!-- Botón de acción -->
    <div class="mt-6 text-center">
      <slot name="action"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlayerCard',
  props: {
    isPlayer: {
      type: Boolean,
      default: true
    },
    playerName: {
      type: String,
      default: 'JUGADOR'
    },
    stats: {
      type: Object,
      default: () => ({
        wins: 0,
        losses: 0,
        precision: 0
      })
    },
    waitingText: {
      type: String,
      default: 'ESPERANDO\nOPONENTE'
    }
  }
}
</script>

<style scoped>
@keyframes pulse-slow {
  0% { opacity: 0.4; }
  50% { opacity: 0.6; }
  100% { opacity: 0.4; }
}

.animate-pulse-slow {
  animation: pulse-slow 4s infinite;
}
</style>
