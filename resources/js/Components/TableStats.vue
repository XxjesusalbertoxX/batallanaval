<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <GameTitle :title="`REGISTRO DE ${typeUpper}`" size="sm" />
      <GameButton @click="$emit('back')" size="sm">VOLVER</GameButton>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-black">
            <th class="table-header">#</th>
            <th class="table-header">FECHA</th>
            <th class="table-header">OPONENTE</th>
            <th class="table-header">DURACIÓN</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="data.length === 0">
            <td colspan="4" class="text-center py-4 text-xs">NO HAY DATOS DE {{ typeUpper }}</td>
          </tr>
          <tr v-for="(item, index) in data" :key="index" class="data-row" :class="rowClass">
            <td class="table-cell">{{ index + 1 }}</td>
            <td class="table-cell">{{ item.date }}</td>
            <td class="table-cell">{{ item.opponent }}</td>
            <td class="table-cell">{{ item.duration }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import GameTitle from '@/Components/GameTitle.vue'
import GameButton from '@/Components/GameButton.vue'

export default {
  name: 'TableStats',
  components: {
    GameTitle,
    GameButton
  },
  props: {
    type: {
      type: String,
      default: 'derrotas',
      validator: (value) => ['victorias', 'derrotas'].includes(value.toLowerCase())
    },
    data: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    typeUpper() {
      return this.type.toUpperCase()
    },
    rowClass() {
      return this.type.toLowerCase() === 'derrotas' ? 'defeat-row' : 'victory-row'
    }
  }
}
</script>

<style scoped>
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

.data-row {
  transition: background-color 0.3s;
}

.defeat-row:hover {
  background-color: rgba(255, 0, 0, 0.1);
}

.victory-row:hover {
  background-color: rgba(0, 255, 0, 0.1);
}
</style>
