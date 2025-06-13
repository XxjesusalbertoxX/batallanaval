<template>
  <div class="board-container" :class="{ 'mini-board': isMini, 'large-board': large }">
    <!-- Coordenadas de columnas (letras) -->
    <div class="coord-row">
      <div class="coord-cell corner-cell"></div>
      <div class="coord-cell" v-for="col in size" :key="`col-${col}`">
        {{ String.fromCharCode(64 + col) }}
      </div>
    </div>
    
    <!-- Filas del tablero con coordenadas -->
    <div v-for="(row, rowIndex) in board" :key="`row-${rowIndex}`" class="board-row">
      <!-- Coordenadas de filas (números) -->
      <div class="coord-cell">{{ rowIndex + 1 }}</div>
      
      <!-- Celdas del tablero -->
      <Cell 
        v-for="(cell, colIndex) in row" 
        :key="`cell-${rowIndex}-${colIndex}`"
        :state="cell.state"
        :is-mini="isMini"
        :is-large="large"
        :is-player-board="isPlayerBoard"
        :disabled="disabled"
        @click="() => handleCellClick(rowIndex, colIndex)"
      />
    </div>
  </div>
</template>

<script>
import Cell from './Cell.vue';

export default {
  name: 'Board',
  components: {
    Cell
  },
  props: {
    size: {
      type: Number,
      default: 8
    },
    board: {
      type: Array,
      required: true,
      // Now board should be an array of arrays containing cells with numeric states (0-3)
      // 0: empty, 1: ship, 2: miss, 3: hit
    },
    isMini: {
      type: Boolean,
      default: false
    },
    large: {
      type: Boolean,
      default: false
    },
    isPlayerBoard: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    handleCellClick(row, col) {
      if (this.disabled) return;
      this.$emit('cell-click', row, col);
    }
  }
}
</script>

<style scoped>
.board-container {
  display: inline-block;
  background-color: rgba(0, 20, 40, 0.5);
  padding: 4px;
  border: 2px solid rgba(0, 180, 255, 0.4);
  box-sizing: border-box;
  line-height: 0; /* Prevent extra spacing between rows */
}

.board-container * {
  box-sizing: border-box;
}

.mini-board {
  transform: scale(0.55);
  transform-origin: top center;
  margin-left: -22%;
  margin-bottom: -40%;
}

.large-board {
  transform: scale(1.2);
  transform-origin: top center;
  margin: 0 auto 70px;
}

.coord-row {
  display: flex;
  color: #00AAFF;
  font-size: 10px;
  height: 24px;
  margin: 0;
  padding: 0;
}

.board-row {
  display: flex;
  height: 24px;
  margin: 0;
  padding: 0;
  line-height: 0; /* Prevent extra space within rows */
}

.coord-cell {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #00AAFF;
  font-size: 10px;
  flex-shrink: 0;
  margin: 0;
  padding: 0;
}

.corner-cell {
  border: none;
}

.large-board .coord-row,
.large-board .board-row {
  height: 30px;
}

.large-board .coord-cell {
  font-size: 12px;
  width: 30px;
  height: 30px;
}

.mini-board .coord-row,
.mini-board .board-row {
  height: 20px;
}

.mini-board .coord-cell {
  width: 20px;
  height: 20px;
  font-size: 8px;
}

@media (max-width: 1280px) {
  .large-board {
    transform: scale(1);
    margin: 0 auto;
  }
  
  .mini-board {
    transform: scale(0.5);
    margin-left: -25%;
  }
}

@media (max-width: 640px) {
  .mini-board {
    transform: scale(0.4);
    margin-left: -30%;
    margin-bottom: -50%;
  }
  
  .large-board {
    transform: scale(0.8);
    margin: 0 auto;
  }
}
</style>
