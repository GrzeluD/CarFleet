<template>
    <div class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max"
            :class="[
                isLoader && 'pointer-events-none',
                disabled && 'opacity-[0.7] pointer-events-none',
                btnType==='primary' && 'bg-arris-btn-primary xl:hover:bg-arris-btn-primaryHover',
                btnType==='secondary' && 'bg-arris-btn-secondary xl:hover:bg-arris-btn-secondaryHover',
                btnType==='warning' && 'bg-arris-btn-warning xl:hover:bg-arris-btn-warningHover',
                btnType==='danger' && 'bg-arris-btn-danger xl:hover:bg-arris-btn-dangerHover',
                btnType==='success' && 'bg-arris-btn-success xl:hover:bg-arris-btn-successHover',
                btnType==='dark' && 'bg-arris-btn-dark xl:hover:bg-arris-btn-darkHover',
                isSmall ? 'max-h-8' : minHeight,
                isOneLaneText && 'whitespace-nowrap'
            ]">
        <div class="animate-spin w-max" v-if="isLoader">
            <svg xmlns="http://www.w3.org/2000/svg" :width="isSmall ? '24' : '32'" :height="isSmall ? '24' : '32'" fill="none" viewBox="0 0 24 24">
                <path stroke="#FFFFFF"
                        stroke-linecap="round"
                        stroke-width="2"
                        d="M12 3a9 9 0 1 0 9 9M19.794 7.5c.096.167.187.337.273.51"/>
            </svg>
        </div>
        <p class="text-text-base-small font-medium" :class="[
            ['primary', 'warning', 'danger', 'success', 'dark'].includes(btnType) && 'text-arris-btn-textPrimary',
            btnType==='secondary' && 'text-arris-btn-textSecondary'
        ]" v-else>
            <slot/>
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    btnType: {
        // type: String,
        // default: 'primary',
        validator(value, props) {
            return ['', 'primary', 'secondary', 'warning', 'danger', 'success', 'dark'].includes(value)
        },
        default: 'primary'
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    isLoader: {
        type: Boolean,
        default: false,
    },
    isOneLaneText: {
        type: Boolean,
        default: true
    },
    isSmall: {
        type: Boolean,
        default: false
    },
    minHeight: {
        type: String,
        default: 'min-h-12'
    },
})
</script>

<style scoped>

</style>
