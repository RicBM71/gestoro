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
                        :items="motivos"
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
        motivos:[],
        status: false,
		registros: false,
        dialog: false,
        motivo_id: 0,
        show_loading: true,
        titulo:"Motivos Devolución"
      }
    },
    mounted()
    {
        axios.get('/mto/motivos')
            .then(res => {
                this.motivos = res.data;
                this.registros = true;
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
            this.$router.push({ name: 'motivo.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'motivo.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.motivo_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/motivos/'+this.motivo_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Motivo eliminado!');
                    this.motivos = response.data;
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
