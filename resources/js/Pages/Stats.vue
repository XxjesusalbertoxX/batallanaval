<template>
  <GameLayout>
    <div class="flex justify-center items-center min-h-screen">
      <GamePanel color="green" class="w-11/12 max-w-4xl">
        <div class="text-center text-[#00FF00] p-5 animate-scanline">
          <GameTitle title="ESTADÍSTICAS DE BATALLA" size="xl" />

          <div class="w-[100px] h-[50px] mx-auto my-5 bg-no-repeat scale-[2] pixel-ship"></div>

          <div class="my-8">
            <p class="text-xs">REGISTRO DE COMBATE NAVAL</p>
            <p class="text-xs">{{ auth?.user?.name || 'ALMIRANTE' }}</p>
          </div>

          <!-- Sección de gráfico -->
          <div v-if="activeTable === null" class="my-10">
            <p class="text-sm mb-4">HISTORIAL DE BATALLAS</p>
            <BarChart :data="chartData" @bar-click="handleBarClick" />
            <p class="text-xs mt-2 animate-blink">HAZ CLICK EN LAS BARRAS PARA VER DETALLES</p>
          </div>

          <!-- Sección de tabla de estadísticas -->
          <div v-else class="my-10">
            <TableStats 
              :type="activeTable" 
              :data="activeTable === 'derrotas' ? defeatData : victoryData" 
              @back="activeTable = null" 
            />
          </div>

          <!-- Botones -->
          <div class="flex justify-center gap-4 mt-8">
            <GameButton @click="$router.push('/')" size="md">VOLVER AL INICIO</GameButton>
            <GameButton @click="refreshStats" size="md" variant="secondary">ACTUALIZAR</GameButton>
          </div>

          <!-- Info Final -->
          <div class="mt-10">
            <p class="animate-blink mb-5 text-xs">GRADO MILITAR: {{ rankTitle }}</p>
            <p class="text-[8px] text-[#007700]">© 2023 BATALLA NAVAL RETRO - REGISTRO DE COMBATE</p>
          </div>
        </div>
      </GamePanel>
    </div>
  </GameLayout>
</template>

<script>
import GameLayout from '@/Components/GameLayout.vue'
import GameTitle from '@/Components/GameTitle.vue'
import GamePanel from '@/Components/GamePanel.vue'
import GameButton from '@/Components/GameButton.vue'
import BarChart from '@/Components/BarChart.vue'
import TableStats from '@/Components/TableStats.vue'

export default {
  name: 'Stats',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    GameButton,
    BarChart,
    TableStats
  },
  props: {
    auth: Object
  },
  data() {
    return {
      activeTable: null,
      stats: {
        wins: 5,
        losses: 12
      },
      victoryData: [
        { date: '2023-11-18', opponent: 'WAVE_RIDER', duration: '10:25' },
        { date: '2023-11-08', opponent: 'SEA_MASTER', duration: '07:45' },
        { date: '2023-10-30', opponent: 'OCEAN_KING', duration: '14:10' }
      ],
      defeatData: [
        { date: '2023-11-15', opponent: 'ADMIRAL_X', duration: '12:45' },
        { date: '2023-11-10', opponent: 'SEAWOLF', duration: '08:30' },
        { date: '2023-11-05', opponent: 'DESTROYER99', duration: '15:20' },
        { date: '2023-10-28', opponent: 'CAPTAIN_HOOK', duration: '09:15' },
        { date: '2023-10-20', opponent: 'SUBMARINE_KING', duration: '11:50' }
      ],
      isLoading: false
    }
  },
  computed: {
    chartData() {
      return [
        { label: 'VICTORIAS', value: this.stats.wins, color: '#00FF00' },
        { label: 'DERROTAS', value: this.stats.losses, color: '#FF0000' }
      ]
    },
    rankTitle() {
      const total = this.stats.wins + this.stats.losses
      const ratio = total > 0 ? this.stats.wins / total : 0
      if (ratio >= 0.9) return 'ALMIRANTE SUPREMO'
      if (ratio >= 0.7) return 'CAPITÁN DE NAVÍO'
      if (ratio >= 0.5) return 'COMANDANTE'
      if (ratio >= 0.3) return 'TENIENTE'
      return 'MARINERO NOVATO'
    }
  },
  methods: {
    handleBarClick(bar) {
      if (bar.label === 'DERROTAS') {
        this.activeTable = 'derrotas';
      } else if (bar.label === 'VICTORIAS') {
        this.activeTable = 'victorias';
      }
    },
    refreshStats() {
      this.isLoading = true
      setTimeout(() => {
        this.stats = {
          wins: Math.floor(Math.random() * 20),
          losses: Math.floor(Math.random() * 10)
        }
        this.isLoading = false
      }, 1000)
    }
  },
  mounted() {
    // this.refreshStats()
  }
}
</script>

<style scoped>
.pixel-ship {
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50" viewBox="0 0 100 50"><rect x="40" y="20" width="60" height="10" fill="%2300FF00"/><rect x="30" y="30" width="60" height="10" fill="%2300FF00"/><rect x="20" y="40" width="70" height="10" fill="%2300FF00"/><rect x="80" y="10" width="10" height="10" fill="%2300FF00"/></svg>');
  image-rendering: pixelated;
  background-size: contain;
  background-repeat: no-repeat;
}

.table-header {
  text-align: left;
  padding: 8px 16px;
  color: #00FF00;
  font-size: 12px;
  text-transform: uppercase;
  border-bottom: 1px solid #00FF00;
}

.table-cell {
  padding: 8px 16px;
  color: #00FF00;
  font-size: 12px;
  border-bottom: 1px solid rgba(0, 255, 0, 0.2);
}

.defeat-row {
  transition: background-color 0.3s;
}

.defeat-row:hover {
  background-color: rgba(255, 0, 0, 0.1);
}

@keyframes scanline {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: 0 100%;
  }
}

.animate-scanline {
  position: relative;
}

.animate-scanline::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, transparent, rgba(0, 255, 0, 0.1), transparent);
  background-size: 100% 100%;
  animation: scanline 4s linear infinite;
  pointer-events: none;
}

@keyframes blink {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.4;
  }
}

.animate-blink {
  animation: blink 1.5s infinite;
}
</style>
