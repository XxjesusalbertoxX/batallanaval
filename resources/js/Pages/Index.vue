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
      
      <!-- Código del juego para compartir -->
      <CodeSpan :code="code" />

      <!-- Paneles de jugadores -->
      <div class="flex flex-col md:flex-row justify-between gap-8">
        <!-- Panel jugador -->
        <GamePanel color="green" title="JUGADOR" class="flex-1">
          <PlayerCard :is-player="true" :player-name="me.user?.name || me.user_id" :stats="me.user">
            <template #name>
              <p>{{ me.user.name }}</p>
            </template>
            
            <template #action>
              <div class="flex flex-col gap-4">
                <GameButton @click="setReady">LISTO</GameButton>
                <GameButton @click="exitGame" variant="danger">SALIR</GameButton>
              </div>
            </template>
          </PlayerCard>
        </GamePanel>

        <!-- Panel oponente -->
        <GamePanel color="red" title="OPONENTE" class="flex-1">
          <PlayerCard :is-player="true" :player-name="opponent.user?.name" :stats="opponent.user">
            <template #name>
              <p>{{ opponent.user.name }}</p>
            </template>
            
            <template #action>
              <GameButton @click="setReady">LISTO</GameButton>
            </template>
          </PlayerCard>
        </GamePanel>

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
import CodeSpan from '@/Components/CodeSpan.vue';
import axios from 'axios';

export default {
  name: 'Index',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    PlayerCard,
    GameButton,
    CodeSpan
  },
  props: {
    gameId:  { type: Number, required: true },
    player:  { type: Object, required: true },
    code:    { type: String, required: true },
    players: { type: Array,  required: true }  // ← nueva prop
  },
  data() {
    return {
      playerName: '',
      playersState: this.players,   // ← inicializo con lo que viene del servidor
      intervalId:  null
    }
  },
  computed: {
    me() {
      return (
        this.playersState.find(p => p.user_id === this.player.id)
        || { user: {}, user_id: null }
      )
    },
    opponent() {
      return (
        this.playersState.find(p => p.user_id !== this.player.id)
        || { user: {}, user_id: null }
      )
    }
  },
  methods: {
    setReady() {
      axios.post(`/game/${this.gameId}/ready`)
        .then(({ data }) => {
          console.log(data.message)
        })
        .catch(err => console.error(err));
    },
    exitGame() {
      console.log('Saliendo de la búsqueda de partida...');
      // Aquí iría la lógica para salir de la partida
      axios.post(`/game/${this.gameId}/exit`)
        .then(response => {
          // Redirigir al usuario a la página principal o donde corresponda
          window.location.href = '/';
        })
        .catch(error => {
          console.error('Error al salir del juego:', error);
        });
    },
    checkGameStatus() {
    console.log('Verificando estado del juego...');

    return axios.get(`/game/${this.gameId}/status`)
      .then(({ data }) => {
        this.playersState = data.players

        if (data.status === 'started') {
          // Redirige manualmente usando inertia
          this.$inertia.visit(`/game/${this.gameId}/play`, {
            data: {
              my_board: data.my_board,
              current_turn_user_id: data.current_turn_user_id,
              message: data.message
            }
          });
        }
      })
      .catch(err => console.error(err))
  }
  },
  mounted() {
    this.checkGameStatus()

    this.intervalId = setInterval(this.checkGameStatus, 15000)

  },
  beforeUnmount() {
    clearInterval(this.intervalId)
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