<template>
  <div class="bar-chart-container">
    <div class="chart">
      <!-- Y-axis labels -->
      <div class="y-axis">
        <div v-for="(label, index) in yAxisLabels" :key="index" class="y-label">
          {{ label }}
        </div>
      </div>
      
      <!-- Canvas for drawing bars -->
      <div class="bars-container">
        <canvas 
          ref="chartCanvas" 
          :width="canvasWidth" 
          :height="canvasHeight"
          @click="handleCanvasClick"
          @mousemove="handleCanvasMouseMove"
          @mouseout="handleCanvasMouseOut"
        ></canvas>
        
        <!-- Labels below the canvas -->
        <div 
          v-for="(bar, index) in data" 
          :key="index" 
          class="bar-label-container"
          :style="{ 
            left: `${getBarPosition(index) + (barWidth / 2) - 30}px`, 
            width: `${barWidth * 1.5}px`
          }"
        >
          <div class="bar-label">{{ bar.label }}: {{ bar.value }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BarChart',
  props: {
    data: {
      type: Array,
      required: true,
      // Expected format: [{ label: 'Wins', value: 10, color: '#00FF00' }, ...]
    },
    maxValue: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      canvasWidth: 0,
      canvasHeight: 0,
      barWidth: 60, // Increased from 40 to 60 for thicker bars
      barMargin: 220, // Increased from 80 to 120 for even greater spacing
      animationId: null,
      hoverIndex: -1,
      pulseValue: 0,
      pulseDirection: 1
    };
  },
  computed: {
    yAxisLabels() {
      const max = this.maxValue || this.calculateMaxValue();
      const interval = Math.ceil(max / 5);
      const labels = [];
      
      for (let i = 0; i <= 5; i++) {
        labels.unshift(i * interval);
      }
      
      return labels;
    }
  },
  mounted() {
    this.initCanvas();
    window.addEventListener('resize', this.handleResize);
    this.startAnimation();
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
    this.stopAnimation();
  },
  watch: {
    data: {
      handler() {
        this.drawChart();
      },
      deep: true
    }
  },
  methods: {
    calculateMaxValue() {
      return Math.max(...this.data.map(item => item.value), 5);
    },
    calculateHeight(value) {
      const max = this.maxValue || this.calculateMaxValue();
      return (value / max) * this.canvasHeight;
    },
    initCanvas() {
      const container = this.$refs.chartCanvas.parentNode;
      this.canvasWidth = container.offsetWidth;
      this.canvasHeight = container.offsetHeight - 40; // Account for labels
      
      // Set responsive barWidth on mobile
      if (window.innerWidth < 600) {
        this.barWidth = 30; // Increased from 20 to 30 for mobile
        this.barMargin = 60; // Increased from 40 to 60 for more mobile spacing
      }
      
      this.drawChart();
    },
    handleResize() {
      this.initCanvas();
    },
    drawChart() {
      if (!this.$refs.chartCanvas) return;
      
      const ctx = this.$refs.chartCanvas.getContext('2d');
      ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
      
      // Draw the bars
      this.data.forEach((bar, index) => {
        const x = this.getBarPosition(index);
        const barHeight = this.calculateHeight(bar.value);
        const y = this.canvasHeight - barHeight;
        
        // Draw the bar
        ctx.fillStyle = bar.color || '#00FF00';
        ctx.strokeStyle = '#00FF00';
        ctx.lineWidth = 1;
        
        // Fill the bar
        ctx.beginPath();
        ctx.rect(x, y, this.barWidth, barHeight);
        ctx.fill();
        ctx.stroke();
        
        // Add glow effects
        if (bar.label === 'DERROTAS') {
          this.drawGlow(ctx, x, y, this.barWidth, barHeight, 'red', this.pulseValue);
        } else if (index === this.hoverIndex) {
          this.drawGlow(ctx, x, y, this.barWidth, barHeight, '#00FF00', 0.8);
        } else {
          this.drawGlow(ctx, x, y, this.barWidth, barHeight, '#00FF00', 0.5);
        }
      });
    },
    drawGlow(ctx, x, y, width, height, color, intensity) {
      const isRed = color === 'red';
      const alpha = isRed ? this.pulseValue / 10 : intensity;
      const spread = isRed ? 5 + this.pulseValue * 2 : 5;
      
      ctx.shadowBlur = spread;
      ctx.shadowColor = isRed ? `rgba(255, 0, 0, ${alpha})` : `rgba(0, 255, 0, ${alpha})`;
      ctx.shadowOffsetX = 0;
      ctx.shadowOffsetY = 0;
      
      ctx.beginPath();
      ctx.rect(x, y, width, height);
      ctx.stroke();
      
      // Reset shadow
      ctx.shadowBlur = 0;
      ctx.shadowColor = 'transparent';
    },
    getBarPosition(index) {
      const totalWidth = (this.barWidth + this.barMargin) * this.data.length - this.barMargin;
      const startX = (this.canvasWidth - totalWidth) / 2;
      return startX + (this.barWidth + this.barMargin) * index;
    },
    startAnimation() {
      const animate = () => {
        // Update pulse effect for DERROTAS bars
        this.pulseValue += 0.05 * this.pulseDirection;
        if (this.pulseValue >= 1) {
          this.pulseValue = 1;
          this.pulseDirection = -1;
        } else if (this.pulseValue <= 0.3) {
          this.pulseValue = 0.3;
          this.pulseDirection = 1;
        }
        
        this.drawChart();
        this.animationId = requestAnimationFrame(animate);
      };
      
      this.animationId = requestAnimationFrame(animate);
    },
    stopAnimation() {
      if (this.animationId) {
        cancelAnimationFrame(this.animationId);
        this.animationId = null;
      }
    },
    handleCanvasClick(event) {
      const barIndex = this.getBarIndexFromPosition(event);
      if (barIndex !== -1) {
        this.$emit('bar-click', this.data[barIndex]);
      }
    },
    handleCanvasMouseMove(event) {
      this.hoverIndex = this.getBarIndexFromPosition(event);
    },
    handleCanvasMouseOut() {
      this.hoverIndex = -1;
    },
    getBarIndexFromPosition(event) {
      const rect = this.$refs.chartCanvas.getBoundingClientRect();
      const x = event.clientX - rect.left;
      
      for (let i = 0; i < this.data.length; i++) {
        const barX = this.getBarPosition(i);
        if (x >= barX && x <= barX + this.barWidth) {
          return i;
        }
      }
      
      return -1;
    },
    handleBarClick(bar) {
      this.$emit('bar-click', bar);
    }
  }
}
</script>

<style scoped>
.bar-chart-container {
  width: 100%;
  height: 300px;
  margin: 20px 0;
  padding: 20px 10px;
  box-sizing: border-box;
}

.chart {
  display: flex;
  height: 100%;
  width: 100%;
  position: relative;
}

.y-axis {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 40px;
  height: 100%;
  color: #00FF00;
  font-size: 12px;
  margin-right: 10px;
  text-align: right;
  padding-right: 5px;
  border-right: 1px solid #00FF00;
}

.y-label {
  position: relative;
}

.bars-container {
  position: relative;
  flex-grow: 1;
  height: calc(100% - 30px);
  border-bottom: 1px solid #00FF00;
}

canvas {
  position: absolute;
  top: 30px; /* Space for value labels */
  left: 0;
  width: 100%;
  height: calc(100% - 30px);
}

.bar-label-container {
  position: absolute;
  bottom: -40px;
  transform: translateX(-50%);
  width: auto; /* Allow natural width instead of constraining */
  white-space: nowrap; /* Keep label text on one line */
}

.bar-label {
  margin-top: 10px;
  color: #00FF00;
  font-size: 12px;
  text-align: center;
  text-transform: uppercase;
}

/* Value is now part of the bar-label, so we can remove this */
.value-label {
  display: none;
}

@media (max-width: 600px) {
  .value-label {
    font-size: 12px;
  }
}

@media (max-width: 600px) {
  .value-label {
    font-size: 12px;
  }
}
</style>
