<template>
  <div 
    class="cell"
    :class="[
      cellClasses,
      { 
        'mini-cell': isMini,
        'large-cell': isLarge,
        'player-board': isPlayerBoard,
        'enemy-board': !isPlayerBoard,
        'disabled': disabled
      }
    ]"
    @click="handleClick"
  >
    <div v-if="showHitMarker" class="hit-marker"></div>
    <div v-if="showMissMarker" class="miss-marker"></div>
  </div>
</template>

<script>
export default {
  name: 'Cell',
  props: {
    state: {
      type: Number,
      default: 0,
      validator: (value) => [0, 1, 2, 3].includes(value)
    },
    isMini: {
      type: Boolean,
      default: false
    },
    isLarge: {
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
  computed: {
    cellClasses() {
      return {
        'empty': this.state === 0,
        'ship': this.state === 1 && this.isPlayerBoard,
        'miss': this.state === 2,
        'hit': this.state === 3,
        'hidden-ship': this.state === 1 && !this.isPlayerBoard
      }
    },
    showHitMarker() {
      return this.state === 3;
    },
    showMissMarker() {
      return this.state === 2;
    }
  },
  methods: {
    handleClick() {
      if (!this.disabled) {
        this.$emit('click');
      }
    }
  }
}
</script>

<style scoped>
.cell {
  width: 24px;
  height: 24px;
  border: 1px solid rgba(0, 180, 255, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  cursor: pointer;
  transition: all 0.1s ease;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.mini-cell {
  width: 20px;
  height: 20px;
}

.large-cell {
  width: 30px;
  height: 30px;
  border-width: 2px;
}

.cell:hover:not(.disabled) {
  background-color: rgba(0, 255, 255, 0.2);
}

/* Estados de las celdas */
.empty {
  background-color: rgba(0, 80, 120, 0.3);
}

.ship {
  background-color: #00CC00;
}

.hidden-ship {
  background-color: rgba(0, 80, 120, 0.3); /* Igual que empty, para ocultar */
}

.hit {
  background-color: #CC0000;
}

.miss {
  background-color: rgba(0, 120, 160, 0.5);
}

/* Marcadores de impacto */
.hit-marker {
  position: absolute;
  width: 70%;
  height: 70%;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5,5 L19,19 M19,5 L5,19" stroke="%23FFCC00" stroke-width="3"/></svg>');
  background-size: contain;
}

.miss-marker {
  position: absolute;
  width: 70%;
  height: 70%;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" fill="none" stroke="%2300CCFF" stroke-width="3"/></svg>');
  background-size: contain;
}

/* Diferencias visuales entre tableros */
.player-board.ship {
  background-color: #00CC00;
}

.enemy-board {
  border-color: rgba(255, 80, 80, 0.4);
}

.disabled {
  cursor: not-allowed;
  opacity: 0.8;
}
</style>
