<template>
    <div class="text_field relative" :class="{'z-[99]': showCopiedMessage}">
        <label :for="uuid" class="text-base font-medium" :class="{'opacity-[0.5]': inactive, 'pointer-events-none': inactive}" v-if="!isLabelInside">{{ label }}</label>
        <p class="absolute top-[10px] pointer-events-none text-arris-textfield-placeholder"
                :class="{'left-10': isSearchField, 'pointer-events-none': inactive, }"
                v-if="placeholder!=='' && value==='' && !isLabelInside">{{ placeholder }}</p>
        <div class="relative" :class="[inputHeight]">
            <input class="peer/input group border border-arris-textfield-border px-4 rounded-md text-arris-textfield-inputText w-full focus:outline-none active:outline-none focus:border-arris-textfield-activeBorder copyable-hover:hidden"
                    spellcheck="false"
                    :class="[
                        errorMessage!==undefined && errorMessage!=='' && 'error border-arris-general-error focus:outline-none',
                        isSearchField && 'pl-10',
                        inactive && 'opacity-[0.5]',
                        hideValue && 'text-transparent',
                        isBgWhite && 'bg-[#FFFFFF]',
                        (isUnitVisible || isClearable) && 'pr-8',
                        (isCopyable && !isCopyableOnHover) && 'pr-12',
                        (isCopyable && isCopyableOnHover) && 'hover:pr-12',
                        (isCopyable && isCopyableOnHover && isCopyIconHovered) && 'pr-12',
                        inputHeight,
                        inputBgColor,
                        additionalInputClasses,
                        inactive && 'pointer-events-none',
                    ]"
                    :readonly="isReadOnly"
                    :type="inputType"
                    :id="uuid"
                    v-model="value"
                    ref="inputRef"
                    :step="inputStep"
                    @blur="$emit('blur')"
                    @focus="$emit('focus')"
                    @keyup="handleKeyUp($event)"
                    @keydown="$emit('handleKeyUp')"
                    @change="$emit('changeInput')"
                    @input="(event) => {
                        $emit('update:modelValue', event.target.value)
                        $emit('modifyInput')
                    }"
                    required />
            <svg class="absolute top-1/2 -translate-y-1/2 left-2 pointer-events-none"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    v-if="isSearchField">
                <path fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7 8C7 11.3666 9.72912 14.0958 13.0957 14.0958C14.4516 14.0958 15.7042 13.653 16.7167 12.9042C15.6061 14.4057 13.1062 16.5 11.0957 16.5C7.72912 16.5 5 13.7708 5 10.4042C5 8.39358 6.69009 5.48947 8.19133 4.37913C7.44264 5.39172 7 6.64417 7 8Z"
                        fill="#EDE9FE"/>
                <path d="M19.8836 19.0942L19.8836 19.0942L15.7869 15.063C16.8574 13.8833 17.5139 12.3311 17.5139 10.6247C17.5134 6.93737 14.4765 3.95 10.7317 3.95C6.98691 3.95 3.95 6.93737 3.95 10.6247C3.95 14.312 6.98691 17.2994 10.7317 17.2994C12.3338 17.2994 13.8047 16.7507 14.9652 15.8366L19.0808 19.8866C19.0808 19.8866 19.0808 19.8866 19.0808 19.8867C19.3023 20.1049 19.6616 20.1049 19.8831 19.8866C20.1056 19.668 20.1055 19.3129 19.8836 19.0942ZM10.7317 16.1801C7.61278 16.1801 5.08577 13.6921 5.08577 10.6247C5.08577 7.55724 7.61278 5.06925 10.7317 5.06925C13.8507 5.06925 16.3777 7.55724 16.3777 10.6247C16.3777 13.6921 13.8507 16.1801 10.7317 16.1801Z"
                        fill="#4C1D95"
                        stroke="#4C1D95"
                        stroke-width="0.1"/>
            </svg>

            <svg class="absolute top-1/2 right-2 -translate-y-1/2 cursor-pointer z-10"
                    :class="{'pointer-events-none': inactive}"
                    width="14"
                    height="14"
                    viewBox="0 0 14 14"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    v-if="isClearable && value!==''"
                    @click="handleClearBtnClicked">
                <path d="M1 1L13 13" stroke="#E10001" stroke-width="2" stroke-linecap="round"/>
                <path d="M1 13L13 1" stroke="#E10001" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div id="icon" class="peer/icon absolute top-1/2 -translate-y-1/2 right-2 z-[2]" :class="[isCopyableOnHover && 'transition-[opacity] ease-in-out duration-300 opacity-0 peer-hover/input:opacity-100 hover:opacity-100']" v-if="isCopyable" @mouseover="isCopyIconHovered= true" @mouseleave="isCopyIconHovered = false">
                <div class="w-8 h-8 flex items-center justify-center transition-colors rounded-lg bg-arris-btn-secondary hover:bg-arris-btn-secondaryHover cursor-pointer" @click="handleCopyInputValueClick"  >
                    <svg width="18" height="25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.66 18.5v-15a2 2 0 0 1 2-2h9.039a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2h-9.04a2 2 0 0 1-2-2Z" stroke="#28559F" stroke-width="1.3"/><path fill="#fff" d="M1.051 4h13.04v19.5H1.051z"/><path d="M1.486 22.5V5a1 1 0 0 1 1-1h2.477v16.5h8.693v2a1 1 0 0 1-1 1H2.486a1 1 0 0 1-1-1Z" fill="#C7D6F0"/><path d="M1.051 21.5v-15a2 2 0 0 1 2-2h9.04a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H3.05a2 2 0 0 1-2-2Z" stroke="#28559F" stroke-width="1.3"/></svg>
                </div>

                <div class="absolute -bottom-[150%] bg-arris-btn-secondary px-4 py-2 rounded-lg cursor-pointer z-[99]" @click="showCopiedMessage=false" v-if="showCopiedMessage">
                    <p class="text-arris-btn-textSecondary">{{copiedMessage}}</p>
                </div>
            </div>

            <p class="absolute top-1/2 -translate-y-1/2 pointer-events-none right-2 text-arris-textfield-inputText"
                    v-if="isUnitVisible">{{ unitValue }}</p>
            <label v-if="isLabelInside" :for="uuid" class="fill_label px-0.5 absolute z-[1] left-4 top-1/2 -translate-y-1/2 pointer-events-none transition-[all] whitespace-nowrap text-footer-link font-overused peer-focus/input:top-[2px] peer-focus/input:-translate-y-[10px] peer-focus/input:text-[12px] peer-focus/input:leading-[20px] peer-focus/input:left-4" :class="[inputBgColor, inactive && 'pointer-events-none',  (modelValue!=='' || isLabelMoved) && 'top-[2px] -translate-y-[10px] text-[12px] leading-[20px] left-4', isSearchField && 'left-8']">{{ label }}</label>
        </div>


        <p class="absolute text-[11px] leading-[14px] -bottom-[14px] text-arris-general-error">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import {computed, ref, watch} from "vue";

const props = defineProps({
    additionalInputClasses: {
        type: String,
        default: ''
    },
    copiedMessage: {
        type: String,
        default: 'Skopiowano'
    },
    errorMessage: {
        type: String,
        default: '',
    },
    id: String,
    inactive: {
        type: Boolean,
        default: false,
    },
    inputHeight: {
        type: String,
        default: 'h-11',
    },
    inputStep: {
      type: Number,
      default: 1,
    },
    isBgWhite: {
        type: Boolean,
        default: false,
    },
    isClearable: {
        type: Boolean,
        default: false,
    },
    isCopyable: {
        type: Boolean,
        default: false,
    },
    isCopyableOnHover: {
        type: Boolean,
        default: false
    },
    isLabelInside: {
        type: Boolean,
        default: false,
    },
    isLabelMoved: {
        type: Boolean,
        default: false
    },
    isUnitVisible: {
        type: Boolean,
        default: false,
    },
    inputBgColor: {
        type: String,
        default: 'bg-arris-inputBox-textFieldBackground'
    },
    label: {
        type: String,
        default: '',
    },
    errorName: {
        type: String,
        default: '',
    },
    hideValue: {
        type: Boolean,
        default: false,
    },
    isSearchField: {
        type: Boolean,
        default: false,
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    unitValue: {
        type: String,
        default: '',
    },
    inputType: {
        type: String,
        default: "text"
    },
    isReadOnly: {
      type: Boolean,
      default: false
    }
});

const emit = defineEmits(['enter', 'esc', 'keyupPressed', 'handleKeyUp', 'update:modelValue', 'blur', 'focus', 'modifyInput', 'changeInput'])

let uuid = ref(props.id ? props.id : getUuid())
let value = ref(props.modelValue);
let showCopiedMessage = ref(false);
const inputRef = ref(null);
const isCopyIconHovered = ref(false);

function handleClearBtnClicked() {
    value.value = '';
}

function focusInput() {
    inputRef.value.focus();
}

defineExpose({
    focusInput,
});

function handleCopyInputValueClick() {
    const inputElement = document.createElement('input');
    inputElement.value = value.value;
    document.body.appendChild(inputElement);
    inputElement.select();
    document.execCommand('copy');
    document.body.removeChild(inputElement);

    showCopiedMessage.value=true;

    setTimeout(() => {
        showCopiedMessage.value = false;
    }, 1500)
}

function handleKeyUp(event) {
    if (event.key === 'Enter') {
        emit('enter');
    } else if (event.key === 'Escape') {
        emit('esc');
    } else {
        emit('keyupPressed', event);
    }
}

function getUuid() {
    const randomNumber = Math.random();
    const hexString = (randomNumber).toString(16).slice(2, 17);
    return hexString
}


watch(() => props.modelValue, (val) => {
    value.value = val;
});

</script>

<style lang="scss" scoped>
.text_field {
  input {
    &:autofill {
      $bgColor: #FFFFFF;
      background-color: v-bind("preparedBgCP");
      opacity: 1;
      background-clip: text;
      -webkit-box-shadow: 0 0 0 30px $bgColor inset !important;
    }
  }
}
</style>
