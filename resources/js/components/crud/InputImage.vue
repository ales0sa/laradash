
<template>
<div class="p-fileupload p-fileupload-advanced p-component">

        <FileUpload name="demo[]" 
                    chooseLabel="Seleccionar archivo"
                    :showUploadButton="false"
                    :showCancelButton="false"
                    accept="image/*" :maxFileSize="1000000"  :customUpload="true" @uploader="myUploader" :auto="true"
        
        >

      <template #empty>

            
            <div class="" v-if="value">
                <div class="p-fileupload-row">
                    <div>
                        <img role="presentation" :alt="value" :src="value.replace('public/','storage/')" :width="previewWidth" />
                    </div>
                    <div> </div>
                    
                    <div>
                      <Button icon="pi pi-times" class="p-button-rounded p-button-danger" style="width: 35px; height: 35px;"/>

                    </div>
                </div>
            </div>

          <p v-if="!value"> Arrastre los archivos aquí.</p>
      </template>

        </FileUpload>
</div>
</template>

<script>

    
    export default {
        props: {
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: String,
                default: {}
            },
            previewWidth: {
                type: Number,
                default: 250
            }
        },
        components: {

        },
        data(){
            return{
                file: null,
            }
        },
        created() {
        },
        mounted () {
          this.file = this.value
        },
        watch: {


        },
        methods: {
        myUploader(event) {

            this.$emit('input', event.files);
        }

        },
        computed: {
        }
    }

</script>

<style>
.p-fileupload-content {
    position: relative;
}
.p-fileupload-row {
    display: flex;
    align-items: center;
}
.p-fileupload-row > div {
    flex: 1 1 auto;
    width: 25%;
}
.p-fileupload-row > div:last-child {
    text-align: right;
}
.p-fileupload-content .p-progressbar {
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
.p-button.p-fileupload-choose {
    position: relative;
    overflow: hidden;
}
.p-button.p-fileupload-choose input[type=file] {
    display: none;
}
.p-fileupload-choose.p-fileupload-choose-selected input[type=file] {
    display: none;
}
.p-fluid .p-fileupload .p-button {
    width: auto;
}
</style>