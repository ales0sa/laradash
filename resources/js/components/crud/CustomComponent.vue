<template>
  <div>
      <component :is="currentEditForm" @setValue="setValue" :input="input" :value="value.value" />
  </div>
</template>
<script>
export default {
  name: 'CustomCompo',
    props: {
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: Object,
                default: {}
            }
    },
  data() {
    return {
      componentToDisplay: null,
    };
  },

  computed: {
    currentEditForm: function() {
      if (this.componentToDisplay) {
        return () => import(`./../../../../../../../resources/js/components/laradash/`+ this.input.columnname +`.vue`);
      }
      return null;
    }
  },
  created() {
    
  },
  mounted()
  {
    this.launchEditForm(this.input.columnname)
  },
  methods: {
    setValue(data){
      console.log('set value in customcopmonent')
      this.$emit('setValue', data)
    },
    launchEditForm(fileName) {
      this.componentToDisplay = fileName;
    }
  }
};
</script>