<template>

<span class="p-float-label p-mt-3">


        <InputText type="text" value=" " v-model="value.value"   id="float-input" />


        <label for="float-input"  >{{ input.label[lang()] }}</label>

</span>
</template>
<script>

    export default {
        props: {
            input: {
                type: Object,
                default: () => []
            },
            value: {
                type: Object,
                default: () => []
            }
        },
        components: {},
        data(){
            return{
            }
        },
        created() {
        },
        mounted () {},
        watch: {},
        methods: {
            lang() {
                return this.$parent.lang
            }
        },
        computed: {
        }
    }

</script>