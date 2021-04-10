<template>
    <Fieldset class="" :legend="input.label[this.lang()]" :key="componentKey">



        <div :class="'p-grid ' + colorize(key)" v-for="(item, key) in items" :id="'sf'+key" :key="key" >


                    <InputLayout :relations="relations" :subForm="subForm"
                     :value="item.content[input.columnname]" :input="input" 
                     v-for="(input, inputk) in subForm[input.columnname].inputs" :key="'subform'+inputk">
                    </InputLayout>


                <div class="p-field p-col-2"> 
                  <span class="p-buttonset">
                    <Button @click="moveUp(key)" icon="pi pi-caret-up"
                    class="p-button-rounded p-button-info p-button-outlined" v-if="key > 0">
                    </Button>



                    <Button @click="moveDown(key)" icon="pi pi-caret-down" class="p-button-rounded p-button-info p-button-outlined" v-if="key < ( items.length - 1 )"></Button>

                                        <Button icon="pi pi-times" @click="removeItem(key)" 
                    class="p-button-rounded p-button-danger p-button-outlined" />
                  </span>
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
    .subform {
        display: flex;
        &__row {
            flex-grow: 1;
        }
        &__buttons-left {
            padding-top: 23px;
            padding-right: 15px;
        }
        &__buttons-right {
            padding-left: 15px;
            display: flex;
            flex-direction: column;
        }
        &___button-down {
            margin-top: auto;
            margin-bottom: 4px;
        }
    }
</style>
<style type="text/css">
    .invoice-box {
  max-width: 800px;
  margin: auto;
  padding: 30px;
  border: 1px solid #eee;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
  font-size: 16px;
  line-height: 24px;
  font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
  color: #555;
}

.colorize {
  border-radius: 10px;
  animation: blinking 1s 1;
}

@keyframes blinking {
        0% {
          background-color: #fff;

        }
        50% {
          background-color: lightgrey;

        }
        100% {
          background-color: #fff;

        }
}

.invoice-box table {
  width: 100%;
  line-height: inherit;
  text-align: left;
}

.invoice-box table td {
  padding: 5px;
  vertical-align: top;
}

.invoice-box table tr td:nth-child(n + 2) {
  text-align: right;
}

.invoice-box table tr.top table td {
  padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
  font-size: 45px;
  line-height: 45px;
  color: #333;
}

.invoice-box table tr.information table td {
  padding-bottom: 40px;
}

.invoice-box table tr.heading td {
  background: #eee;
  border-bottom: 1px solid #ddd;
  font-weight: bold;
}

.invoice-box table tr.details td {
  padding-bottom: 20px;
}

.invoice-box table tr.item td {
  border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
  border-bottom: none;
}

.invoice-box table tr.item input {
  padding-left: 5px;
}

.invoice-box table tr.item td:first-child input {
  margin-left: -5px;
  width: 100%;
}

.invoice-box table tr.total td:nth-child(2) {
  border-top: 2px solid #eee;
  font-weight: bold;
}

.invoice-box input[type="number"] {
  width: 60px;
}

@media only screen and (max-width: 600px) {
  .invoice-box table tr.top table td {
    width: 100%;
    display: block;
    text-align: center;
  }

  .invoice-box table tr.information table td {
    width: 100%;
    display: block;
    text-align: center;
  }
}

/** RTL **/
.rtl {
  direction: rtl;
  font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial,
    sans-serif;
}

.rtl table {
  text-align: right;
}

.rtl table tr td:nth-child(2) {
  text-align: left;
}

</style>