<template>
  <div class="min-h-screen bg-[#001428] font-pixel relative overflow-hidden p-4">
    <!-- Overlay con efecto escaneo -->
    <div class="absolute inset-0 pointer-events-none animate-moveLines overlay"></div>
    
    <!-- Contenido de la página -->
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'GameLayout'
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

.font-pixel {
  font-family: 'Press Start 2P', cursive;
}

.overlay {
  background: repeating-linear-gradient(
    180deg,
    rgba(0, 0, 0, 0) 0,
    rgba(0, 0, 0, 0.1) 1px,
    rgba(0, 0, 0, 0) 2px
  );
}

@keyframes moveLines {
  from { background-position: 0 0; }
  to { background-position: 0 100%; }
}

.animate-moveLines {
  animation: moveLines 6s linear infinite;
}
</style>
