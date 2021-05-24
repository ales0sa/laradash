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
                default: () => {}
            },

    },
  data() {
    return {
      componentToDisplay: null,
    };
  },
  computed: {
    currentEditForm: function() {
      if (this.componentToDisplay) {
        return () => import(`./../../../../../../resources/js/components/laradash/`+ this.$route.params.vc +`.vue`);
      }
      return null;
    }
  },
  created() {
      
  },
    watch: {
        $route(to, from) {
          this.componentToDisplay = null
          console.log(to)
          console.log(from)
          this.launchEditForm(to.params.vc)
        }
  },
  mounted()
  {
    this.componentToDisplay = null
    this.launchEditForm(this.$route.params.vc)
  },
  methods: {
    launchEditForm(fileName) {
      this.componentToDisplay = fileName;
    }
  }
};
</script>