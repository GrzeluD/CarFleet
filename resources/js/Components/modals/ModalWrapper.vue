<template>
    <div class="w-screen h-screen fixed top-0 left-0 bg-arris-modal-tint flex items-center justify-center z-[999] backdrop-blur-sm">
        <div class="absolute w-screen h-screen top-0 left-0 z-[1]" @click="$emit('close')"></div>
        <div class="rounded rounded-lg overflow-hidden relative z-[2]" :class="modalStyles">
            <div class="w-full min-h-8 bg-arris-modal-topBarBackground px-4 py-2 flex items-center justify-between">
                <p class="text text-arris-modal-topBarTextColor max-w-[calc(100%_-_16px)]" :class="[topBarTextStyles]">{{topBarDesc}}</p>
                <button class="border border-[transparent] transition-[border] rounded-full p-1 hover:xl:border-arris-modal-topBarTextColor" @click="$emit('close')">
                    <svg class="fill-arris-modal-topBarTextColor" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5.54688" y="4" width="20.5061" height="2.18728" rx="1.09364" transform="rotate(45 5.54688 4)"></rect>
                        <rect width="20.5061" height="2.18728" rx="1.09364" transform="matrix(-0.707107 0.707107 0.707107 0.707107 18.5 4)" ></rect></svg>
                </button>
            </div>
            <div class="w-full h-full bg-arris-modal-contentBackground p-4 relative">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
    const props = defineProps({
        modalStyles: {
            type: String,
            default: 'w-max h-max'
        },
        topBarDesc: {
            type: String,
            default: ''
        },
        topBarTextStyles: {
            type: String,
            default: 'text-[12px] leading-[20px]'
        }
    })
</script>

<style scoped>

</style>
