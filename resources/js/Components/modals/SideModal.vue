<template>
    <div class="fixed z-[998]" :class="[preparedClasses[side].transition, isVisible ? preparedClasses[side].isVisible : preparedClasses[side].isHidden, isFullScreen ? 'w-screen h-screen top-0' : `${nonFullScreenClasses} w-max h-max`]">
        <div class="w-screen h-screen absolute inset-0 relative" :class="{'bg-arris-modal-tint': isTint}" @click="$emit('close')" v-if="isFullScreen"></div>
        <div class="z-[2]" :class="modalStyles">
            <div class="w-full h-8 flex justify-end items-center">
                <button class="border border-[transparent] transition-[border] rounded-full p-1 hover:xl:border-arris-modal-topBarTextColor" @click="$emit('close')">
                    <svg class="fill-arris-modal-topBarTextColor" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5.54688" y="4" width="20.5061" height="2.18728" rx="1.09364" transform="rotate(45 5.54688 4)"></rect>
                        <rect width="20.5061" height="2.18728" rx="1.09364" transform="matrix(-0.707107 0.707107 0.707107 0.707107 18.5 4)" ></rect></svg>
                </button>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    isFullScreen: {
        type: Boolean,
        default: false
    },
    isTint: {
        type: Boolean,
        default: true,
    },
    isVisible: {
        type: Boolean,
        default: false,
    },
    modalStyles: {
        type: String,
        default: ''
    },
    nonFullScreenClasses: {
        type: String,
        default: 'top-[200px]'
    },
    side: {
        type: String,
        default: 'left' //z której strony pojawia się modal
    }
})

const preparedClasses = {
    left: {
        transition: 'transition-[left]',
        isVisible: 'left-0',
        isHidden: '-left-[100vw]'
    },
    right: {
        transition: 'transition-[right]',
        isVisible: 'right-0',
        isHidden: '-right-[100vw]'
    }
}
</script>

<style scoped>

</style>
