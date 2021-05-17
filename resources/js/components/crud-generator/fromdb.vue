<template>
    <div>
    <h5>Generate CRUD from DataBase</h5>

<div class="p-grid">
	<div class="p-col-3">
        <Listbox v-model="selectedTable" :options="tables"
            @change="getColumns()"
        />
    
     </div>
	<div class="p-col-9"> 
        <Button label="Create JSON File" @click="generate()" />

        <div>
            <textarea style="width: 100%; height: 500px;">{{ jsonIdea }}</textarea>
        </div>

        </div>

</div>



    </div>
</template>

<script>
import axios from 'axios'
import EventBus from './../../service/event-bus';

export default {
    data() {
	return {
		selectedTable: null,
        jsonIdea: null,
		tables: null,
        dbtypes: {
                    "bigint": 'bigInteger',
                    "string": 'text',
                    "text": 'textarea',
                    "integer": 'number',                    
                    "biginteger": 'bigInteger',
                    "datetime": 'datetime',
                    "date": 'date',
                    "boolean": 'boolean'
                }
        }
        


    },
    methods: {
        generate() {

                let formData = new FormData()

                formData.append('data', JSON.stringify(this.jsonIdea)); 

                axios.post('/adm/jsonfromdb', formData).then((response) => {

                        if(response.data.status == 'success'){

                                    this.$toast.add({severity:'success', summary: 'Success', detail: response.data.message, life: 3000})
                                    EventBus.$emit('reloadMenu');
                                    this.$router.push({ name: 'cre', params: { file: this.selectedTable+'.json' }});
                        }
                        
             
                }).catch((error) => {
                    console.log(error)
                })

        },
        getColumns() {
            console.log(this.selectedTable)
            axios.get('/adm/dbtables/'+this.selectedTable).then((response) => {
                    
                    if(response.data.status == 'success') {
                        
                        const arrayHasIndex = (array, index) => { 
                        
                            if(index in array){
                                return 1
                            }else{
                                return 0
                            }

                        };

                        let hasId = arrayHasIndex(response.data.columns, 'id')
                        let hasUuid = arrayHasIndex(response.data.columns, 'uuid')
                        let hasTimeStamps = arrayHasIndex(response.data.columns, 'created_at')
                        let hasSoftDel = arrayHasIndex(response.data.columns, 'deleted_at')

                        //console.log(hasId)

                        let parsedInputs = []
                        const array1 =  Object.keys(response.data.columns);

                        //console.log(response.data.columns)

                        Array.prototype.forEach.call(array1, child => {
                            if(child == 'id' || child == 'created_at' || child == 'deleted_at' || child == 'updated_at'){
                                //return;
                                console.log(child)
                            }else{
                                parsedInputs.push({
                                isCollapsed: true,                               
                                columnname: child,
                                icon: '',
                                type: this.dbtypes[response.data.columns[child].type],
                                visible: 1,
                                gridcols: 12,
                                visible_edit: 1,
                                label: { es: child, en: child },
                                unique: 0,
                                default: response.data.columns[child].props.default,
                                nullable: response.data.columns[child].props.notnull,
                                validate: 1,
                                max: '',
                                min: '',
                                tabledata: '',
                                tablekeycolumn: '',
                                tabletextcolumn: '',
                                translatable: 0,
                                multiple: false
                            })

                            }
                           
                            /*console.log()
                            console.log(response.data.columns[child].type)*/

                        });





                        this.jsonIdea = { 
                            
                            'table': {
                                                "id": hasId,
                                                "uuid": hasUuid,
                                                "tablename": this.selectedTable,
                                                "name": {
                                                    "es": this.selectedTable,
                                                    "en": this.selectedTable
                                                },
                                                "timestamps": hasTimeStamps,
                                                "softDeletes": hasSoftDel,
                                                "menu_show": 1,
                                                "singlepage": false,
                                                "slug": 0,
                                                "slug_global": 0,
                                                "icon": "pi pi-file",
                                                "whoCan": ['root']
                            },
                            'inputs': parsedInputs

                            }

                    }
        });


        }
    },
    mounted(){

        console.log('Loading tables...')
        
        axios.get('/adm/dbtables').then((response) => {
                    
                   if(response.data.status == 'success') {
                        
                        this.tables = Object.values(response.data.tables)

                    }
        });

    }


}
</script>