<template>
    <div class="p-field">
        <span v-if="this.input.multiple"  class="p-float-label p-mt-3">

        <MultiSelect  
            v-model="selected"
            @change="setSelect($event.value)"
            :value="value.value"
            optionLabel="text" 
            optionValue="key"
            dataKey="key"
            :filter="true"
            :options="options" 
            :placeholder="'Seleccione ' + input.label[lang()]">
        </MultiSelect>
        </span>

        <span class="p-float-label p-mt-3" v-else>

        <Dropdown 
            :id="input.columnname"
            v-model="selected"
            @change="setSelect($event.value)"
            :value="value.value"
            optionLabel="text" 
            optionValue="key"
            :options="options" 
            dataKey="key"
            :filter="true"
            >
        </Dropdown>
                 <label :for="input.columnname">{{ input.label[lang()] }}</label>
        </span>

        
    </div>
</template>
<script>

    export default {
        props: {
            input: {
                type: Object,
                default: {}
            },
            relations: {
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
                selected: null,
                options: [],
                optionsA: [],                
                mode: this.input.valueoriginselector
            }
        },
        created() {


            //this.value.value = this.input.default
            //console.log(this.value.value)

        },
        mounted () {


            if(this.input.multiple){
                

            if(this.value.value){
                let arr = [];
                if(this.value.value.includes(',')){

                    
                    arr = this.value.value.split(',');

                    this.selected = arr.map((i) => String(i));//this.value.value.split(',')
                }else{
                    arr[0]  = String(this.value.value)
                    this.selected = arr///this.value.value
                }


            }



            }else{

            this.selected = String(this.value.value)
            }

            if (this.mode == 'table') {
                    const obj = this.relations[this.input.tabledata]
               
                    var result = Object.keys(obj).map(function (key) { 
                        return {'key': String(key), 'text': obj[key]}; 
                    }); 
        
                    this.options = result  

            }else if(this.mode == 'values'){
                this.options = this.input.options
            }

        },
        watch: {},
        methods: {
            setSelect(event){
                    console.log(event)
                    
                    this.value.value = event
            },
            lang() {
                return 'es' //document.documentElement.lang
            }
        },
        computed: {
        }
    }

</script>
