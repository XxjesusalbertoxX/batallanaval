<template>
  <button 
    :class="[
      'font-pixel transition-all duration-200',
      variant === 'primary' ? 'bg-[#00334d] text-[#00FF00] border-[#00FF00]' : 
      variant === 'danger' ? 'bg-[#330000] text-[#FF0000] border-[#FF0000]' : 
      variant === 'disabled' ? 'bg-[#330000] text-[#770000] border-[#770000] opacity-50 cursor-not-allowed' :
      'bg-[#00334d] text-[#00FF00] border-[#00FF00]',
      size === 'sm' ? 'py-1 px-2 text-xs' : 
      size === 'md' ? 'py-2 px-4 text-xs' : 
      size === 'lg' ? 'py-4 px-8 text-base' :
      'py-2 px-4 text-xs',
      fullWidth ? 'w-full' : '',
      !disabled ? 'hover:bg-[#004d73]' : '',
      variant === 'primary' && !disabled ? 'hover:-translate-y-0.5 hover:shadow-[0_5px_0px_#003300]' : '',
      variant === 'danger' && !disabled ? 'hover:bg-[#550000]' : '',
      'border-2'
    ]"
    :style="`${!disabled ? 'text-shadow: 1px 1px #003300; box-shadow: 0 3px 0px #003300;' : ''}`"
    :disabled="disabled || variant === 'disabled'"
    @click="$emit('click')"
  >
    <slot>BOTÓN</slot>
  </button>
</template>

<script>
export default {
  name: 'GameButton',
  props: {
    variant: {
      type: String,
      default: 'primary', // primary, danger, disabled
      validator: (value) => ['primary', 'danger', 'disabled'].includes(value)
    },
    size: {
      type: String,
      default: 'md', // sm, md, lg
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
      type: Boolean,
      default: false
    },
    fullWidth: {
      type: Boolean,
      default: false
    }
  }
}
</script>
