<template>
    <div class="flex items-start">
        <input type="file" @change="uploadFiles" :multiple="isMultiple" :id="uuid" class="w-0 h-0"/>
        <label :for="uuid" class="mr-4">
            <Btn :btnType="btnType">
                <p>{{desc}}</p>
            </Btn>
        </label>
        <div class="min-h-full" v-if="files.length>0 && isUploadedFilesListVisible">
            <p class="text-[14px] leading-[16px] font-medium">{{isMultiple ? uploadedMultiFilesDesc : uploadedSingleFileDesc}}:</p>
            <ul>
                <li v-for="(item, index) in uploadedFiles" :key="index">
                    <p class="text-[14px] leading-[16px]">{{index+1}}. {{ item.name }}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import Btn from "@/components/buttons/Btn.vue";
import { ref } from "vue";

const files = ref([]);

function getUuid() {
    const randomNumber = Math.random();
    const hexString = (randomNumber).toString(16).slice(2, 17);
    return hexString
}

const props = defineProps({
    btnType: {
        type: String,
        default: 'primary'
    },
    desc: {
        type: String,
        default: 'Dodaj plik'
    },
    id: {
        type: String,
        default: ''
    },
    isMultiple: {
        type: Boolean,
        default: false
    },
    isUploadedFilesListVisible: {
        type: Boolean,
        default: true
    },
    uploadedMultiFilesDesc: {
        type: String,
        default: 'Dodane pliki'
    },
    uploadedSingleFileDesc: {
        type: String,
        default: 'Dodany plik'
    },
})
const emit = defineEmits(['onUpload', 'error']);

const uploadFiles = (event) => {
    files.value = [...event.target.files];
    emit('input', files.value);
};
const uploadedFiles = files;

const uuid = props.id ? props.id : getUuid();


</script>

