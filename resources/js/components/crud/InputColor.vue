<template>
<span class="p-float-label p-mt-3">

    <ColorPicker v-model="value.value" />

</span>
</template>
<script>

    export default {
        props: {
            submitted: { type: Boolean, default: false },
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: Object,
                default: {}
            }
        },
        components: {},
        data(){
            return{
                val: this.value,
                validationErrors: this.$parent.validationErrors
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
<style lang="scss" scoped>

</style>
