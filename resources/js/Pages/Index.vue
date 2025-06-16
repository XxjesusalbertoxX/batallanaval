<template>
  <GameLayout>
    <!-- Título del juego -->
    <GameTitle title="BATALLA NAVAL" size="xl" />

    <!-- Contenedor VS -->
    <div class="w-full max-w-7xl mx-auto">
      <!-- Banner VS -->
      <div class="relative flex justify-center items-center my-4">
        <div class="absolute w-32 h-32 bg-[#00FF00] rounded-full opacity-25 animate-ping"></div>
        <span class="text-5xl text-[#FF0000] font-bold z-10 animate-blink" 
              style="text-shadow: 3px 3px 0px #00334d, -1px -1px 0px #FFFFFF;">
          VS
        </span>
      </div>

      <!-- Paneles de jugadores -->
      <div class="flex flex-col md:flex-row justify-between gap-8">
        <!-- Panel jugador -->
        <GamePanel color="green" title="JUGADOR" class="flex-1">
          <PlayerCard :is-player="true" :player-name="player.name" :stats="player">
            <template #name>
              <input v-model="player.name" 
                     class="bg-[#00334d] text-[#00FF00] border border-[#00FF00] px-2 max-w-[150px] font-pixel text-right" />
            </template>
            
            <template #action>
              <GameButton>LISTO</GameButton>
            </template>
          </PlayerCard>
        </GamePanel>

        <!-- Panel oponente -->
        <GamePanel color="red" title="OPONENTE" class="flex-1">
          <PlayerCard 
            :is-player="false" 
            player-name="???" 
            wait-text="ESPERANDO OPONENTE"
          >
            <template #action>
              <GameButton variant="disabled">ESPERANDO</GameButton>
            </template>
          </PlayerCard>
        </GamePanel>
      </div>

      <!-- Botón para iniciar -->
      <div class="text-center mt-10">
        <GameButton 
          size="lg" 
          :disabled="!playerName.trim()"
          @click="startGame"
        >
          INICIAR BATALLA
        </GameButton>
      </div>
      
      <!-- Pie de página -->
      <div class="text-center mt-8 text-[8px] text-[#007700]">
        BATALLA NAVAL RETRO - PREPARADO PARA EL COMBATE
      </div>
    </div>
  </GameLayout>
</template>

<script>
import GameLayout from '@/Components/GameLayout.vue';
import GameTitle from '@/Components/GameTitle.vue';
import GamePanel from '@/Components/GamePanel.vue';
import PlayerCard from '@/Components/PlayerCard.vue';
import GameButton from '@/Components/GameButton.vue';
import axios from 'axios';

export default {
  name: 'Index',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    PlayerCard,
    GameButton
  },
  props: {
    gameId: {
      type: Number,
      required: true
    },
    player: {
      type: Object,
      required: true
    },
    code: {
      type: String,
      required: true
    } 
  },
  data() {
    return {
      playerName: '',
      playerStats: {
        wins: 0,
        losses: 0,
        accuracy: 0
      }
    }
  },
  methods: {
    startGame() {
      if (!this.playerName.trim()) {
        return;
      }
      
      console.log('Iniciando batalla...');
      // Aquí iría la lógica para iniciar la partida
    },
    checkGameStatus() {
      // Aquí iría la lógica para verificar el estado del juego
      console.log('Verificando estado del juego...');

      axios.get(`/api/games/${this.gameId}/status`)
        .then(response => {
          console.log('Estado del juego:', response.data);
        })
        .catch(error => {
          console.error('Error al verificar el estado del juego:', error);
        });
    }
  }
}
</script>

<style>
@keyframes ping {
  0% {
    transform: scale(1);
    opacity: 0.25;
  }
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ping {
  animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Inputs */
input {
  outline: none;
  font-size: 12px;
}
</style>