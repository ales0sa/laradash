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
                type: Array,
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
            
            this.getCustomDash()
        },
        mounted() {
            console.log('Dashboard mounted.')
        },
        methods: {
            getCustomDash(){
                if(this.value[0]['name'] == 'developer'){
                    return 'SampleDash'
                }else{
                    return this.value[0]['name'].charAt(0).toUpperCase() + this.value[0]['name'].slice(1)
                }
            }


        }
    }
</script>
