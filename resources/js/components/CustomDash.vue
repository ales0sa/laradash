<template>
  <div>

      <component :is="currentEditForm"  />
  </div>
</template>
<script>
export default {
  name: 'CustomDash',
    props: {
            
            value: {
                type: String,
                default: 'SampleDash'
            }
    },
  data() {
    return {
      componentToDisplay: this.value,
    };
  },

  computed: {
    currentEditForm: function() {
      if (this.componentToDisplay) {
        return () => import(`./../../../../../../resources/js/components/laradash/`+ this.value +`.vue`);
      }
      return null;
    }
  },
  created() {
    
  },
  mounted()
  {
    //this.launchEditForm(this.input.columnname)
  },
  methods: {
    setValue(data){
      //console.log('set value in customcopmonent')
      //this.$emit('setValue', data)
    },
    launchEditForm(fileName) {
      this.componentToDisplay = fileName;
    }
  }
};
</script>