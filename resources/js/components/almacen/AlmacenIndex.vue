<template>
    <div v-if="registros">
		<my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-card>
            <v-card-title>
                <h2>{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-container>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-data-table
                        :headers="headers"
                        :items="this.almacenes"
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
import MenuOpe from './MenuOpe'
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
    },
    data () {
      return {
        headers: [
          {
            text: 'Nombre',
            align: 'left',
            value: 'name'
          },
          {
            text: 'Acciones',
            align: 'Center',
            value: ''
          }
        ],
        almacenes:[],
        status: false,
		registros: false,
        dialog: false,
        almacen_id: 0,
        titulo:"Ubicaciones"
      }
    },
    mounted()
    {

        axios.get('/mto/almacenes')
            .then(res => {
                this.almacenes = res.data;
                this.registros = true;
            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    methods:{
        create(){
            this.$router.push({ name: 'almacen.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'almacen.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.almacen_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/almacenes/'+this.almacen_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Ubicación eliminada!');
                    this.almacenes = response.data;
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
