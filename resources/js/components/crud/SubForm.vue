<template>
    <Fieldset class="" :legend="input.label[this.lang()]" :key="componentKey">
        <div :class="'p-grid ' + colorize(key)" v-for="(item, key) in items" :id="'sf'+key" :key="key" >
                <div class="p-col-1">
                  <span class="p-buttonset p-py-3" style="">
                    <Button @click="moveUp(key)" icon="pi pi-caret-up"
                    
                    :disabled="key == 0">
                    </Button>

                    <Button @click="moveDown(key)" icon="pi pi-caret-down" 
                    
                    :disabled="key == items.length - 1"></Button>

                    <Button icon="pi pi-times" @click="removeItem(key)" 
                    class="p-button-danger p-button-outlined"
                    />

                  </span>
                </div>
                <div class="p-field p-col-11 p-col-lg-11 p-col-md-11">
                    <div class="p-grid">
                        <InputLayout :relations="relations" :subForm="subForm"
                        :value="item.content[input.columnname]" :input="input" 
                        v-for="(input, inputk) in subForm[input.columnname].inputs" :key="'subform'+inputk">
                        </InputLayout>
                    </div>

                </div>
        </div>
        
             <div class="d-sm-flex align-items-center justify-content-center p-mt-3">
                <Button type="button" @click="addItem()" class="p-button p-button-success p-mr-2 p-button p-component p-text-center" icon="pi pi-plus" label="Añadir">
                </Button>
            </div>
    </Fieldset>
</template>
<script>

    export default {
        name:"SubForm",
        props: {
            input: {
                type: Object,
                default: {}
            },
            relations: {
                default: {}
            },
            subForm:  {
                
            },              
            value: {
                default: {}
            },
        },
        components: {

        },
        data(){
            return{
                items: [],
                moved: null,
                animation: 'colorize',
                componentKey: 0,
                
            }
        },
        created() {


            let items = {}
            this.inputs = this.subForm[this.input.columnname].inputs
            //this.items =  this.value.value ?? []
            console.log(this.value)
            if(this.subForm[this.input.columnname].content) {
                 this.subForm[this.input.columnname].content.forEach(contentItem => {
                    console.log(contentItem)
                    let newItem = {
                        content: {}
                    }
                    Object.keys(contentItem).forEach(key => {
                        newItem.content[key] = {
                            value: contentItem[key],
                            errors: []
                        }
                    });
                    this.items.push(newItem)

                 });
             /* try {
                    this.subForm[this.input.columnname].content.forEach(contentItem => {
                    let newItem = {
                        content: {}
                    }
                    Object.keys(contentItem).forEach(key => {
                        newItem.content[key] = {
                            value: contentItem[key],
                            errors: []
                        }
                    });
                    this.items.push(newItem)
                });

              } catch (error) {
                  console.log(error)
              }*/

            }
            //console.log(this.items)
           // this.mapItems()

        },
        mounted () {},
        watch: {
           items: {
                deep: true,
                handler() {
                    
                    this.value.value = this.items
                }
            }
        },
  filters: {

  },
        methods: {
            colorize(key){
              if(key == this.moved) return 'colorize'
            },
            lang() {
                return document.documentElement.lang
            },
            mapItems() {
/*
               // console.log(this.items)
                if (Object.prototype.toString.call( this.items ) == '[object Array]') {
                    this.items.forEach(item => {
                       //console.log(item)
                            try {
                        this.subForm[this.input.columnname].inputs.forEach(input => {
                            if (!item.content[input.columnname]) {
                                item.content[input.columnname] = {}
                                this.$set(item.content[input.columnname], 'value', '')
                                this.$set(item.content[input.columnname], 'errors', [])
                            }
                        });
                        } catch (error) {
                          console.error(error);
                          // expected output: ReferenceError: nonExistentFunction is not defined
                          // Note - error messages will vary depending on browser
                        }


                    });
                } else {
                    this.items = []
                }*/
            },
            addItem() {
                this.inputs = this.subForm[this.input.columnname].inputs
                let newItem = {
                    content: {}
                }
                this.inputs.forEach(input => {
                    newItem.content[input.columnname] = {}
                    this.$set(newItem.content[input.columnname], 'value', input.default)
                    this.$set(newItem.content[input.columnname], 'errors', [])
                });
                if ( !Array.isArray(this.items) ) {
                    this.items = []
                }
                this.items.push(newItem)
            },
            removeItem(index) {
                this.items.splice(index, 1);
            },
            move(arr, old_index, new_index) {

              if (new_index >= arr.length) {
                  var k = new_index - arr.length + 1;
                  while (k--) {
                      arr.push(undefined);
                  }
              }
              arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
              this.componentKey += 1
              return arr; // for testing

            },
            moveUp(index) {

                this.move(this.items, index, index -1);

            },
            moveDown(index) {
                
                this.move(this.items, index, index + 1);
                this.colorize(index + 1)

            }
        },
        computed: {

    
        }
    }
</script>
<style lang="scss" scoped>

</style>
<style type="text/css">
  

</style>