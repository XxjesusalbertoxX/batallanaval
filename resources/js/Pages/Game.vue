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
                <div v-for="i in 5" :key="`enemy-ship-${i}`" 
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
            <div v-if="lastResult" :class="{
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
              <span>{{ playerShipsRemaining }}/5</span>
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
                @cell-click="handlePlayerBoardClick" 
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
      // Preparación para integración con API
      gameId: null,
      playerId: null,
      connectionStatus: 'online',
      lastActionTimestamp: null
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
    this.initializeBoards();

    // Scroll al final de los logs
    this.$nextTick(() => {
      this.scrollToEndOfLogs();
    });

    // Simular conexión con la API
    this.lastActionTimestamp = Date.now();

    // Preparar para desconexión
    window.addEventListener('beforeunload', this.handleBeforeUnload);
  },
  beforeUnmount() {
    clearInterval(this.timer);
    window.removeEventListener('beforeunload', this.handleBeforeUnload);
  },
  methods: {
    // Métodos del juego principal
    initializeBoards() {
      // Reset game state
      this.playerBoard = Array(8).fill().map(() => Array(8).fill({ state: 0 }));
      this.enemyBoard = Array(8).fill().map(() => Array(8).fill({ state: 0 }));
      this.playerShipsRemaining = 5;
      this.enemyShipsRemaining = 5;
      this.isPlayerTurn = true;
      this.turnCount = 1;
      this.hits = 0;
      this.lastResult = null;
      this.seconds = 0;
      this.gameTime = '00:00';
      this.battleLogs = [
        '» BATALLA INICIADA',
        '» COLOCA TUS BARCOS',
        '» ESPERA EL DESPLIEGUE ENEMIGO'
      ];
      
      clearInterval(this.timer);
      this.startTimer();
      
      // Colocando naves del jugador como ejemplo
      // (en un juego real, el jugador las colocaría)
      const playerShips = [
        [0, 0], [0, 1], [0, 2],  // Barco 1
        [2, 3], [3, 3],          // Barco 2
        [5, 2], [5, 3], [5, 4],  // Barco 3
        [7, 6],                  // Barco 4
        [2, 7], [3, 7], [4, 7]   // Barco 5
      ];
      
      playerShips.forEach(([row, col]) => {
        this.playerBoard[row][col] = { state: 1 }; // Estado 1 = barco
      });

      // Simulando naves enemigas ocultas
      const enemyShips = [
        [1, 1], [1, 2], [1, 3],  // Barco 1
        [3, 4], [4, 4],          // Barco 2
        [6, 0], [6, 1], [6, 2],  // Barco 3
        [4, 6],                  // Barco 4
        [0, 5], [0, 6], [0, 7]   // Barco 5
      ];

      // En realidad no mostraríamos estas posiciones al jugador
      enemyShips.forEach(([row, col]) => {
        this.enemyBoard[row][col] = { state: 1 }; // Estado 1 = barco (oculto en tablero enemigo)
      });
      
      this.addBattleLog('» TODOS LOS BARCOS DESPLEGADOS');
      this.addBattleLog('» COMIENZA LA BATALLA');
      
      // Para integración con API: Crear un nuevo juego
      this.createGameSession();
    },
    
    handlePlayerBoardClick(row, col) {
      // Puede ser útil para colocar barcos inicialmente
      console.log(`Clic en tablero del jugador: ${row}, ${col}`);
      
      // Aquí se podría implementar la lógica para colocar barcos manualmente
    },
    
    handleEnemyBoardClick(row, col) {
      if (!this.isPlayerTurn) return;
      
      const cell = this.enemyBoard[row][col];
      
      // Solo podemos disparar a casillas con estado 0 o 1
      if (cell.state > 1) {
        // Ya se disparó a esta casilla
        return;
      }
      
      // Registrar el último tiempo de acción para la API
      this.lastActionTimestamp = Date.now();
      
      if (cell.state === 1) { // Hay un barco
        // Actualizar estado: 1 (barco) -> 3 (barco golpeado)
        this.enemyBoard[row][col] = { state: 3 };
        this.hits++;
        this.enemyShipsRemaining--;
        this.lastResult = 'hit';
        this.addBattleLog(`» ¡IMPACTO CONFIRMADO EN [${String.fromCharCode(65 + col)}${row + 1}]!`);
        
        // Para integración con API: Enviar acción del jugador
        this.sendPlayerAction('attack', row, col, true);
        
        // Comprobar victoria
        if (this.enemyShipsRemaining === 0) {
          this.gameOver(true);
          return;
        }
      } else { // Es una casilla vacía
        // Actualizar estado: 0 (vacía) -> 2 (disparo fallido)
        this.enemyBoard[row][col] = { state: 2 };
        this.lastResult = 'miss';
        this.addBattleLog(`» Disparo fallido en [${String.fromCharCode(65 + col)}${row + 1}]`);
        
        // Para integración con API: Enviar acción del jugador
        this.sendPlayerAction('attack', row, col, false);
      }
      
      // Cambiar turno
      this.isPlayerTurn = false;
      setTimeout(this.enemyTurn, 1500);
    },
    
    enemyTurn() {
      // Simular ataque enemigo
      let row, col;
      let validMove = false;
      
      while (!validMove) {
        row = Math.floor(Math.random() * 8);
        col = Math.floor(Math.random() * 8);
        
        const cell = this.playerBoard[row][col];
        // Solo puede disparar a casillas con estado 0 o 1
        if (cell.state <= 1) {
          validMove = true;
        }
      }
      
      // En una implementación real, esto vendría de la API
      // this.receiveEnemyAction('attack', row, col);
      
      if (this.playerBoard[row][col].state === 1) { // Hay un barco
        // Actualizar estado: 1 (barco) -> 3 (barco golpeado)
        this.playerBoard[row][col] = { state: 3 };
        this.playerShipsRemaining--;
        this.addBattleLog(`» ¡IMPACTO ENEMIGO EN [${String.fromCharCode(65 + col)}${row + 1}]!`);
        
        // Comprobar derrota
        if (this.playerShipsRemaining === 0) {
          this.gameOver(false);
          return;
        }
      } else { // Es una casilla vacía
        // Actualizar estado: 0 (vacía) -> 2 (disparo fallido)
        this.playerBoard[row][col] = { state: 2 };
        this.addBattleLog(`» Ataque enemigo fallido en [${String.fromCharCode(65 + col)}${row + 1}]`);
      }
      
      this.turnCount++;
      this.isPlayerTurn = true;
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
    async createGameSession() {
      try {
        // En una implementación real, esto haría una llamada a la API
        console.log('Creando sesión de juego...');
        /* 
        const response = await fetch('/api/games', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            playerName: this.playerName,
          }),
        });
        const data = await response.json();
        this.gameId = data.gameId;
        this.playerId = data.playerId;
        */
        
        // Simulación
        this.gameId = 'game_' + Math.floor(Math.random() * 1000);
        this.playerId = 'player_' + Math.floor(Math.random() * 1000);
        
        console.log(`Juego creado: ${this.gameId}`);
      } catch (error) {
        console.error('Error al crear el juego:', error);
        this.connectionStatus = 'offline';
      }
    },
    
    async sendPlayerAction(actionType, row = null, col = null, hit = null) {
      if (this.connectionStatus === 'offline') return;
      
      try {
        // En una implementación real, esto haría una llamada a la API
        console.log(`Enviando acción: ${actionType} ${row},${col}`);
        /*
        await fetch(`/api/games/${this.gameId}/actions`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            playerId: this.playerId,
            actionType,
            row,
            col,
            hit,
            timestamp: Date.now()
          }),
        });
        */
      } catch (error) {
        console.error('Error al enviar acción:', error);
        this.connectionStatus = 'offline';
      }
    },
    
    async sendGameResult(result) {
      if (this.connectionStatus === 'offline') return;
      
      try {
        // En una implementación real, esto haría una llamada a la API
        console.log(`Enviando resultado: ${result}`);
        /*
        await fetch(`/api/games/${this.gameId}/result`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            playerId: this.playerId,
            result,
            stats: this.gameStats,
            timestamp: Date.now()
          }),
        });
        */
      } catch (error) {
        console.error('Error al enviar resultado:', error);
      }
    },
    
    async saveGameState() {
      if (this.connectionStatus === 'offline') return;
      
      try {
        // En una implementación real, esto haría una llamada a la API
        console.log('Guardando estado del juego...');
        /*
        await fetch(`/api/games/${this.gameId}/save`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            playerId: this.playerId,
            playerBoard: this.playerBoard,
            enemyBoard: this.enemyBoard,
            playerShipsRemaining: this.playerShipsRemaining,
            enemyShipsRemaining: this.enemyShipsRemaining,
            isPlayerTurn: this.isPlayerTurn,
            turnCount: this.turnCount,
            gameTime: this.seconds,
            hits: this.hits,
            timestamp: Date.now()
          }),
        });
        */
      } catch (error) {
        console.error('Error al guardar estado:', error);
      }
    },
    
    async receiveEnemyAction(actionType, row, col) {
      // En una implementación real, esto sería llamado por un websocket o polling
      console.log(`Recibiendo acción enemiga: ${actionType} ${row},${col}`);
      
      // Procesar la acción recibida del enemigo
      if (actionType === 'attack') {
        // Implementar lógica de ataque enemigo aquí
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
