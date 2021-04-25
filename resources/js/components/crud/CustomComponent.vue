<template>
  <div>
    <component :is="currentEditForm" />
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
        return () => import(`./../../../../../../../resources/js/admin/`+ this.input.columnname +`.vue`);
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
    launchEditForm(fileName) {
      this.componentToDisplay = fileName;
    }
  }
};
</script>