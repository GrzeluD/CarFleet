<template>
    <div class="switch" :class="{ disabled: disabled}">
        <input :id="uuid" type="checkbox" class="toggle" v-model="value" @change="(event) => change(event)"/>
    </div>
</template>

<script setup>
import {ref, watch} from "vue";

const props = defineProps({
    stateOffBackgroundColor: {
        type: String,
        default: '#979797'
    },
    stateOnBackgroundColor: {
        type: String,
        default: '#0AA61B'
    },
    id: {
        type: String,
        default: '',
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false
    },
    stateOffCircleBackground: {
        type: String,
        default: '#FFFFFF'
    },
    stateOnCircleBackground: {
        type: String,
        default: '#FFFFFF'
    },
    circleRadius: {
        type: Number,
        default: 24,
    },
    switcherWidth: {
        type: Number,
        default: 48,
    },
    switcherHeight: {
        type: Number,
        default: 28,
    },
    verticalSpacing: {
        type: Number,
        default: 2,
    }
})

const value = ref(props.modelValue);
let uuid = ref(props.id ? props.id : getUuid())

function getUuid() {
    const randomNumber = Math.random();
    const hexString = (randomNumber).toString(16).slice(2, 17);
    return hexString
}

const emit = defineEmits(['update:modelValue'])

function change(event) {
    emit('update:modelValue', event.target.checked)
}

watch(() => props.modelValue, (val) => {
    value.value = val;
});


</script>

<style lang="scss" scoped>
.switch {
  display: inline-block;
  vertical-align: middle;
  width: v-bind('props.switcherWidth+"px"');
  height: v-bind('props.switcherHeight+"px"');

  &.disabled {
    pointer-events: none;
    opacity: .5;

    .toggle {
      &:checked {
        background: #D8D8D8;
      }
    }
  }

  .toggle {
    width: 100%;
    height: 100%;
    position:relative;
    border-radius:20px;
    appearance:none;
    background: v-bind('props.stateOffBackgroundColor');
    outline:none;
    cursor:pointer;
    transition: left 0.3s, background .3s;
    -moz-appearance: none;
    -webkit-appearance: none;

    &:before {
      content:"";
      width: v-bind('props.circleRadius+"px"');
      height: v-bind('props.circleRadius+"px"');
      border-radius: 50%;
      position:absolute;
      //top: v-bind('(props.switcherHeight-props.circleRadius)/2+"px"');
      top: 50%;
      transform: translateY(-50%);
      left: v-bind('props.verticalSpacing+"px"');
      background: v-bind('props.stateOffCircleBackground');
      transition: left 0.3s, background .3s;
    }

    &:checked {
      background: v-bind('props.stateOnBackgroundColor');

      &:before {
        left: v-bind('((props.switcherWidth-props.circleRadius)-props.verticalSpacing)+"px"');
        background: v-bind('props.stateOnCircleBackground');
      }
    }
  }

}

</style>
