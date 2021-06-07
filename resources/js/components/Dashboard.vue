<template>
    <div class="container">

 
  <CustomDash :value="getCustomDash()"
   />


    </div>
</template>

<script>
    import CustomDash     from './CustomDash'

    export default {
        props: {
            value: {
                type: String,
                default: ''
            },

        },
        data() {
            return {
                dashcompo: 'SampleDash',
            }
        },
        components: { CustomDash },
        created(){
            
        },
        mounted() {
            console.log('Component mounted.')
            this.getCustomDash()
        },
        methods: {
            getCustomDash(){
               return this.value[0]['name'].charAt(0).toUpperCase() + this.value[0]['name'].slice(1)
            }


        }
    }
</script>
