<template>
  <GameLayout>
    <div class="flex justify-center items-center min-h-screen">
      <GamePanel color="green" class="w-11/12 max-w-4xl">
        <div class="text-center text-[#00FF00] p-5 animate-scanline">
          <GameTitle title="BATALLA NAVAL" size="xl" />
          
          <div class="w-[100px] h-[50px] mx-auto my-5 bg-no-repeat scale-[2] transform pixel-ship"></div>
          
          <div class="my-8">
            <p class="text-xs leading-loose">PREPÁRATE PARA LA GUERRA NAVAL</p>
            <p class="text-xs leading-loose">HUNDE LA FLOTA ENEMIGA</p>
          </div>
          
          <GameButton size="lg" @click="startGame">INICIAR JUEGO</GameButton>
          
          <div class="mt-10">
            <p class="text-xs leading-relaxed">CÓMO JUGAR:</p>
            <p class="text-xs leading-relaxed">1. COLOCA TUS BARCOS</p>
            <p class="text-xs leading-relaxed">2. TURNA DISPAROS</p>
            <p class="text-xs leading-relaxed">3. HUNDE TODOS LOS BARCOS ENEMIGOS PARA GANAR</p>
          </div>
          
          <div class="mt-10">
            <p class="animate-blink mb-5 text-xs">INSERTA MONEDA PARA CONTINUAR</p>
            <p class="text-[8px] text-[#007700]">© 2023 BATALLA NAVAL RETRO</p>
          </div>
        </div>
      </GamePanel>
    </div>
  </GameLayout>
</template>

<script>
import GameLayout from '@/Components/GameLayout.vue';
import GameTitle from '@/Components/GameTitle.vue';
import GamePanel from '@/Components/GamePanel.vue';
import GameButton from '@/Components/GameButton.vue';

export default {
  name: 'Welcome',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    GameButton
  },
  data() {
    return {
      playerName: '',
      gameReady: false
    }
  },
  methods: {
    startGame() {
      console.log('Iniciando juego...');
      // Agregar lógica de navegación aquí cuando la página del juego esté lista
    }
  }
}
</script>

<style>
.pixel-ship {
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50" viewBox="0 0 100 50"><rect x="40" y="20" width="60" height="10" fill="%2300FF00"/><rect x="30" y="30" width="60" height="10" fill="%2300FF00"/><rect x="20" y="40" width="70" height="10" fill="%2300FF00"/><rect x="80" y="10" width="10" height="10" fill="%2300FF00"/></svg>');
  image-rendering: pixelated;
}

@keyframes scanline {
  0% { background-position: 0 0; }
  100% { background-position: 0 100%; }
}

.animate-scanline {
  animation: scanline 4s linear infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

.animate-blink {
  animation: blink 1s infinite;
}
</style>