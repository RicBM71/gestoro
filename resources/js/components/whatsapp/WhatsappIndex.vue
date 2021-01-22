<template>
    <div>
        <loading :show_loading="show_loading"></loading>
		<my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-card>
            <v-card-title>
                <h2>{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-container v-if="registros">
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-data-table
                        :headers="headers"
                        :items="whatsapps"
                        rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <td>{{ props.item.nombre }}</td>
                                <td class="justify-center layout px-0">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="editItem(props.item.id)"
                                    >
                                        edit
                                    </v-icon>
                                    <v-icon
                                        small
                                        @click="openDialog(props.item.id)"
                                    >
                                    delete
                                    </v-icon>
                                </td>
                            </template>
                            <template slot="pageText" slot-scope="props">
                                Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                            </template>
                        </v-data-table>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data () {
      return {
        headers: [
          {
            text: 'Texto',
            align: 'left',
            value: 'texto'
          },
          {
            text: 'Acciones',
            align: 'Center',
            value: ''
          }
        ],
        whatsapps:[],
        status: false,
		registros: false,
        dialog: false,
        whatsapp_id: 0,
        show_loading: true,
        titulo:"Plantllas WhatsApps"
      }
    },
    mounted()
    {
        axios.get('/mto/whatsapps')
            .then(res => {
                
                this.whatsapps = res.data;
                if (this.whatsapps.length > 0)
                    this.$router.push({ name: 'whatsapp.edit', params: { id: this.whatsapps[0].id } })
                else
                    this.$router.push({name: 'whatsapp.create' })

                //this.registros = true;
            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
            .finally(()=> {
                this.show_loading = false;
            });
    },
    methods:{
        create(){
            this.$router.push({ name: 'whatsapp.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'whatsapp.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.whatsapp_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/whatsapps/'+this.whatsapp_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Plantilla eliminada!');
                    this.whatsapps = response.data;
                }
                })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },

    }
  }
</script>
