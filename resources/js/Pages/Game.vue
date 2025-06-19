<template>
  <GameLayout>
    <!-- Encabezado -->
    <GameStatusBar 
      title="BATALLA NAVAL" 
      :status="isPlayerTurn ? 'TU TURNO' : 'TURNO ENEMIGO'" 
      :info="`TIEMPO: ${gameTime}`"
      class="mb-4"
    />

    <!-- Contenedor principal con grid de 3 columnas verticales -->
    <div class="game-grid">
      <!-- COLUMNA IZQUIERDA: Historial de ataques (400px) -->
      <div class="grid-col-left">
        <GamePanel color="green" class="h-full flex flex-col">
          <div class="flex justify-between items-center mb-2">
            <div class="text-[#00FF00] text-sm truncate">HISTORIAL</div>
            <div class="flex items-center">
              <span class="text-[#00FF00] text-xs">T: {{ turnCount }}</span>
            </div>
          </div>
          
          <!-- Contenedor de scroll con altura fija -->
          <div class="battle-log flex-grow overflow-y-auto overflow-x-hidden text-xs text-[#00AA00] p-2 bg-[#001800] mb-2">
            <p v-for="(log, index) in battleLogs" :key="index" class="mb-2 break-all">
              {{ log }}
            </p>
            <div ref="logEnd"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <div class="text-[#00FF00] text-xs">HITS: {{ hits }}</div>
            <div class="text-[#00FF00] text-xs">{{ Math.round((hits / (turnCount-1)) * 100) || 0 }}%</div>
          </div>
        </GamePanel>
      </div>

      <!-- COLUMNA CENTRAL: Tablero enemigo grande (1fr) -->
      <div class="grid-col-center">
        <!-- Tablero principal del enemigo -->
        <GamePanel color="red" class="h-full">
          <div class="flex justify-between items-center mb-3">
            <div class="text-[#FF0000] text-lg">RADAR ENEMIGO</div>
            <div class="flex items-center">
              <span class="text-[#FF0000] text-sm mr-2">BARCOS:</span>
              <div class="flex space-x-1">
                <div v-for="i in 15" :key="`enemy-ship-${i}`" 
                     :class="i <= enemyShipsRemaining ? 'bg-[#FF0000]' : 'bg-[#330000]'"
                     class="w-4 h-4"></div>
              </div>
            </div>
          </div>
          
          <div class="flex-grow flex justify-center items-center relative">
            <div v-if="!isPlayerTurn" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
              <span class="text-[#FF0000] text-3xl animate-blink">ESPERA TU TURNO</span>
            </div>
            <Board 
              :size="8" 
              :board="enemyBoard" 
              :is-mini="false" 
              :is-player-board="false"
              :disabled="!isPlayerTurn"
              :large="true"
              @cell-click="handleEnemyBoardClick" 
            />
          </div>
          
          <div class="mt-3 text-center">
            <div :class="{
              'text-[#FF0000]': lastResult === 'miss',
              'text-[#00FF00]': lastResult === 'hit'
            }" class="text-xl animate-pulse">
              {{ lastResult === 'hit' ? '¡IMPACTO!' : 'AGUA...' }}
            </div>
          </div>
        </GamePanel>
      </div>

      <!-- COLUMNA DERECHA: Mini tablero del jugador y opciones (300px) -->
      <div class="grid-col-right">
        <div class="flex flex-col h-full gap-4">
          <!-- Información del jugador y mini tablero -->
          <GamePanel color="green" class="flex-[6_0_0%] min-h-0">
            <div class="text-[#00FF00] mb-2 text-center font-bold truncate">{{ playerName }}</div>
            <div class="flex justify-between text-xs text-[#00FF00] mb-1">
              <span>BARCOS:</span>
              <span>{{ playerShipsRemaining }}/15</span>
            </div>
            <div class="w-full h-2 bg-[#003300] mb-3">
              <div class="h-full bg-[#00FF00]" :style="{ width: playerShipsRemaining * 20 + '%' }"></div>
            </div>
            
            <div class="text-center text-[#00FF00] text-xs mb-2">TU FLOTA</div>
            <div class="flex justify-center">
              <Board 
                :size="8" 
                :board="playerBoard" 
                :is-mini="true" 
                :is-player-board="true"
              />
            </div>
          </GamePanel>
          
          <!-- Opciones de juego -->
          <GamePanel color="green" class="flex-[5_0_0%] min-h-0 overflow-y-auto">
            <div class="text-center text-[#00FF00] text-xs mb-2">OPCIONES</div>
            <ul class="space-y-1">
              <li>
                <GameButton 
                  variant="danger" 
                  size="sm" 
                  @click="surrender" 
                  :full-width="true"
                >
                  RENDIRSE
                </GameButton>
              </li>
              <li>
                <GameButton size="sm" :full-width="true">
                  NUEVO OPONENTE
                </GameButton>
              </li>
              <li>
                <GameButton size="sm" :full-width="true" @click="initializeBoards">
                  REINICIAR
                </GameButton>
              </li>
              <li>
                <GameButton size="sm" :full-width="true">
                  AYUDA
                </GameButton>
              </li>
              <li>
                <GameButton size="sm" :full-width="true">
                  SALIR
                </GameButton>
              </li>
            </ul>
          </GamePanel>
        </div>
      </div>
    </div>
  </GameLayout>
</template>

<script>
import Board from '@/Components/Board.vue';
import GameLayout from '@/Components/GameLayout.vue';
import GamePanel from '@/Components/GamePanel.vue';
import GameButton from '@/Components/GameButton.vue';
import GameStatusBar from '@/Components/GameStatusBar.vue';

export default {
  name: 'Game',
  components: {
    Board,
    GameLayout,
    GamePanel,
    GameButton,
    GameStatusBar
  },
  props: {
    gameId: Number,
    my_board: Array,
    enemy_board: Array,
    current_turn_user_id: Number
  },
  data() {
    return {
      playerName: 'ALMIRANTE',
      playerBoard: Array(8).fill().map(() => Array(8).fill({ state: 0 })), // Usar estados numéricos
      enemyBoard: Array(8).fill().map(() => Array(8).fill({ state: 0 })), // Usar estados numéricos
      playerShipsRemaining: 5,
      enemyShipsRemaining: 5,
      isPlayerTurn: true,
      turnCount: 1,
      gameTime: '00:00',
      hits: 0,
      lastResult: null,
      timer: null,
      seconds: 0,
      battleLogs: [
        '» BATALLA INICIADA',
        '» COLOCA TUS BARCOS',
        '» ESPERA EL DESPLIEGUE ENEMIGO'
      ],
      lastProcessedTurnId: null,
      lastProcessedResult: null,
      // Preparación para integración con API
      playerId: null,
      localGameId: this.gameId, // ✅ puedes modificar este sin error
      connectionStatus: 'online',
    };
  },
  computed: {
    hitPercentage() {
      if (this.turnCount <= 1) return 0;
      return Math.round((this.hits / (this.turnCount - 1)) * 100);
    },
    gameStats() {
      // Preparado para enviar a la API
      return {
        playerId: this.playerId,
        gameId: this.gameId,
        turnCount: this.turnCount,
        hits: this.hits,
        accuracy: this.hitPercentage,
        gameTime: this.seconds,
        playerShipsRemaining: this.playerShipsRemaining,
        enemyShipsRemaining: this.enemyShipsRemaining
      };
    }
  },
  mounted() {
    this.startTimer();
    this.startHeartbeat();
    this.startGameStatusPolling(); // ✅
    this.initializeBoards();

    this.playerId = this.$page.props.auth.user.id; // si lo necesitas
    this.localGameId = this.$page.props.gameId;

    this.$nextTick(() => {
      this.scrollToEndOfLogs();
    });

    window.addEventListener('beforeunload', this.handleBeforeUnload);
  },
  beforeUnmount() {
    clearInterval(this.timer);
    clearInterval(this.heartbeatInterval);
    clearInterval(this.gameStatusInterval); // ✅
    window.removeEventListener('beforeunload', this.handleBeforeUnload);
  },
  methods: {
    // Métodos del juego principal
    initializeBoards() {
      this.startHeartbeat();
      this.startGameStatusPolling();      
    },
    startHeartbeat() {
          this.heartbeatInterval = setInterval(async () => {
            try {
              await axios.get(`/game/${this.$page.props.gameId}/heartbeat`);
              console.log('Heartbeat enviado');
            } catch (error) {
              console.error('Error en heartbeat:', error);
              this.connectionStatus = 'offline';
            }
          }, 5000); // cada 5 segundos
        },
    startGameStatusPolling() {
    this.gameStatusInterval = setInterval(async () => {
      try {
        const { data } = await this.checkStatusGame();

        // 1️⃣ Cambio de turno
        const newPlayerTurn = data.current_turn_user_id === this.playerId;
        if (newPlayerTurn !== this.isPlayerTurn) {
          this.isPlayerTurn = newPlayerTurn;
          this.turnCount++;
        }

        // 2️⃣ Resultado de tiro
        if (data.last_move_id && data.last_move_id !== this.lastProcessedTurnId) {
          // Solo lo proceso una vez
          this.lastProcessedTurnId = data.last_move_id;
          
          // Asumo que tu API devuelve también la posición del último tiro:
          const { x, y, hit } = data.last_move;  
          // Marco la casilla en el enemyBoard:
          this.enemyBoard[x][y].state = hit ? 2 : 3;  // 2=impacto, 3=agua
          
          // Log y contador de hits
          this.lastResult = hit ? 'hit' : 'miss';
          this.addBattleLog(hit ? '» ¡IMPACTO!' : '» AGUA...');
          if (hit) this.hits++;
        }

        // 3️⃣ Actualizo contadores de barcos restantes (si cambian)
        const self  = data.players.find(p => p.is_self);
        const enemy = data.players.find(p => !p.is_self);
        if (self.ships_remaining  !== this.playerShipsRemaining) {
          this.playerShipsRemaining = self.ships_remaining;
        }
        if (enemy.ships_remaining !== this.enemyShipsRemaining) {
          this.enemyShipsRemaining = enemy.ships_remaining;
        }

      } catch (err) {
        console.error('Error al checar el estado del juego:', err);
        clearInterval(this.gameStatusInterval);
        this.connectionStatus = 'offline';
      }
    }, 10000);
  },
    async checkStatusGame() {
      try {
        const response = await axios.get(`/game/${this.gameId}/status`);
        return response;
      } catch (err) {
        console.error('Error en checkStatusGame:', err);
        throw err;
      }
    },
    async handleEnemyBoardClick(row, col) {
      if (!this.isPlayerTurn) return;
      const cell = this.enemyBoard[row][col];
      if (cell.state > 1) return; // ya disparado

      // Enviar ataque al backend
      try {
        await this.sendPlayerAttack(row, col);
        // desactivar turno inmediatamente
        this.isPlayerTurn = false;
      } catch (err) {
        console.error('Error enviando ataque:', err);
      }
    },
    
    surrender() {
      if (confirm("¿Estás seguro que deseas rendirte?")) {
        // Para integración con API: Enviar acción de rendición
        this.sendPlayerAction('surrender');
        this.gameOver(false);
      }
    },
    
    gameOver(playerWon) {
      clearInterval(this.timer);
      if (playerWon) {
        this.addBattleLog('» ¡VICTORIA! TODOS LOS BARCOS ENEMIGOS DESTRUIDOS');
        alert("¡VICTORIA! Has hundido todos los barcos enemigos.");
        
        // Para integración con API: Enviar resultado del juego
        this.sendGameResult('victory');
      } else {
        this.addBattleLog('» DERROTA. TU FLOTA HA SIDO DESTRUIDA');
        alert("DERROTA. Tu flota ha sido destruida.");
        
        // Para integración con API: Enviar resultado del juego
        this.sendGameResult('defeat');
      }
    },
    
    // Métodos de utilidad
    startTimer() {
      this.timer = setInterval(() => {
        this.seconds++;
        const minutes = Math.floor(this.seconds / 60);
        const secs = this.seconds % 60;
        this.gameTime = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
      }, 1000);
    },
    
    addBattleLog(message) {
      this.battleLogs.push(message);
      this.$nextTick(() => {
        this.scrollToEndOfLogs();
      });
    },
    
    scrollToEndOfLogs() {
      if (this.$refs.logEnd) {
        this.$refs.logEnd.scrollIntoView({ behavior: 'smooth' });
      }
    },
    
    handleBeforeUnload(event) {
      // Preguntar al usuario si está seguro de abandonar la partida
      event.preventDefault();
      event.returnValue = '';
      
      // Para integración con API: Guardar estado del juego antes de salir
      this.saveGameState();
    },
    
    // Métodos para integración futura con API
    
    async sendPlayerAttack(row, col) {
      if (this.connectionStatus === 'offline') return;
      try {
        const response = await axios.post(`/game/${row}/${col}/attack`);
        // Opcional: procesar respuesta inmediata si devuelve algo
      } catch (error) {
        console.error('Error al enviar ataque:', error);
        this.connectionStatus = 'offline';
      }
    }
  }
}
</script>

<style>
.game-grid {
  display: grid;
  grid-template-columns: 400px 1fr 300px;
  grid-gap: 16px;
  height: calc(100vh - 100px); /* Adjust height based on header */
}

.grid-col-left, .grid-col-center, .grid-col-right {
  height: 100%;
  overflow: hidden;
}

.battle-log {
  min-height: 200px;
  max-height: calc(100% - 60px);
}
</style>
