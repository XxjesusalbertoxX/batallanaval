<template>
  <GameLayout>
    <!-- Título del juego -->
    <GameTitle title="BATALLA NAVAL" size="xl" />

    <!-- Contenedor principal -->
    <div class="w-full max-w-7xl mx-auto">
      <!-- Banner de Búsqueda -->
      <div class="relative flex justify-center items-center my-4">
        <div class="absolute w-32 h-32 bg-[#00FF00] rounded-full opacity-25 animate-ping"></div>
        <span class="text-5xl text-[#FF0000] font-bold z-10 animate-blink" 
              style="text-shadow: 3px 3px 0px #00334d, -1px -1px 0px #FFFFFF;">
          UNIRSE
        </span>
      </div>

      <!-- Panel de búsqueda de juego -->
      <div class="flex justify-center">
        <GamePanel color="green" title="INTRODUCIR CÓDIGO DE PARTIDA" class="w-full max-w-2xl">
          <div class="flex flex-col items-center p-6">
            <!-- Input grande para código de partida -->
            <input 
              v-model="gameCode" 
              class="bg-[#00334d] text-[#00FF00] border-4 border-[#00FF00] px-4 py-3 text-center mb-6 w-full font-pixel text-2xl tracking-wider" 
              placeholder="INGRESA CÓDIGO"
              maxlength="6"
            />
            
            <!-- Mensaje de estado -->
            <div class="text-center mb-6 text-[#00FF00] font-pixel">
              {{ statusMessage }}
            </div>
            
            <!-- Botón para unirse -->
            <GameButton 
              size="lg" 
              :disabled="!isValidGameCode"
              @click="joinGame"
            >
              UNIRSE A LA BATALLA
            </GameButton>
          </div>
        </GamePanel>
      </div>

      <!-- Enlace para volver -->
      <div class="text-center mt-10">
        <GameButton @click="goBack" variant="secondary">REGRESAR</GameButton>
      </div>
      
      <!-- Pie de página -->
      <div class="text-center mt-8 text-[8px] text-[#007700]">
        BATALLA NAVAL RETRO - ÚNETE A LA LUCHA
      </div>
    </div>
  </GameLayout>
</template>

<script>
import GameLayout from '@/Components/GameLayout.vue';
import GameTitle from '@/Components/GameTitle.vue';
import GamePanel from '@/Components/GamePanel.vue';
import GameButton from '@/Components/GameButton.vue';
import axios from 'axios';

export default {
  name: 'SearchGame',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    GameButton
  },
  data() {
    return {
      gameCode: '',
      statusMessage: 'INGRESA EL CÓDIGO PROPORCIONADO POR TU OPONENTE'
    }
  },
  computed: {
    isValidGameCode() {
      // Puedes implementar una validación más compleja según tus requerimientos
      return this.gameCode.trim().length >= 4;
    }
  },
  methods: {
    joinGame() {
      
      this.$inertia.post('/game/join', {
        code: this.gameCode
      })
      .catch(error => {
        if (!this.isValidGameCode) {
          this.statusMessage = 'CÓDIGO INVÁLIDO. INTENTA DE NUEVO';
          return;
        }
        console.error('Fallo al unirse:', error.response?.data || error.message);
      });

      
      this.statusMessage = 'CONECTANDO...';
      console.log('Uniendo a la partida con código:', this.gameCode);
      // Aquí iría la lógica para unirse a la partida con el código
    },
    goBack() {
      // Navegar de vuelta a la página principal
      this.$inertia.visit('/');
    }
  }
}
</script>

<style>
@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.animate-blink {
  animation: blink 2s ease-in-out infinite;
}

/* Inputs específicos para esta página */
input {
  outline: none;
  transition: all 0.3s;
}

input:focus {
  box-shadow: 0 0 15px #00FF00;
}

input::placeholder {
  color: #007700;
  opacity: 0.6;
}
</style>
