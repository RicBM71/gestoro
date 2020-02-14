<template>
    <div v-if="registros">
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-card>
            <v-layout row wrap>
                <v-flex xs6></v-flex>
                <v-flex xs6>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Buscar"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-flex>
            </v-layout>
            <br/>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                    :headers="headers"
                    :items="albaranes"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.alb_ser }}</td>
                            <td>{{ formatDate(props.item.fecha_alb) }}</td>
                            <td>{{ props.item.notas }}</td>
                            <td class="text-xs-right">{{ totalImpLinea(props.item.albalins) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                            <td>{{ props.item.factura }}</td>
                            <td>{{ formatDate(props.item.fecha_fac) }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item.id)"
                                >
                                    edit
                                </v-icon>


                                <v-icon
                                v-if="props.item.eje_fac==0"
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
        </v-card>
    </div>
</template>
<script>
import moment from 'moment';
import MyDialog from '@/components/shared/MyDialog'
  export default {
    components: {
        'my-dialog': MyDialog,
    },
    data () {
      return {
        albaran:{
            id: 0,
            cliente_id: 0,
            eje_fac: 0
        },
        titulo:"Albaranes",
        search:"",
        pagination:{
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "fecha_albaran",
        },
        headers: [
          {
            text: 'Albarán',
            align: 'center',
            value: 'albaran'
          },
          {
            text: 'Fecha',
            align: 'left',
            value: 'fecha_alb'
          },
          {
            text: 'Observaciones',
            align: 'left',
            value: 'notas'
          },
          {
            text: 'Importe',
            align: 'right',
            value: 'importe',
            sortable: false
          },
          {
            text: 'Factura',
            align: 'Left',
            value: 'factura'
          },
          {
            text: 'F. Factura',
            align: 'Left',
            value: 'fecha_fac'
          },
          {
            text: 'Acciones',
            align: 'Center',
            value: ''
          }
        ],
        albaranes:[],
        status: false,
		registros: false,
        dialog: false,
        producto_id: 0,

      }
    },
    props:{
        cliente_id: Number
    },
    mounted()
    {
        axios.get('/mto/clientes/'+this.cliente_id+'/albaranes')
            .then(res => {

                this.albaranes = res.data.albaranes;
                this.registros = true;
            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    methods:{
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        totalImpLinea(lineas){
            var total = 0;
            lineas.map((lin) =>
            {
                var imp = parseFloat(lin.importe);
                var iva = parseFloat(lin.poriva)
                var irpf= parseFloat(lin.porirpf)
                total += (imp + (imp * iva / 100) - (imp * irpf / 100));

            })
            return total.toFixed(2);
        },
        create(){
            this.$router.push({ name: 'albaran.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'albaran.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.albaran_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/ventas/albacabs/'+this.albaran_id,{_method: 'delete', cliente_id: this.cliente_id})
                .then(response => {

                this.albaranes = response.data;
                this.$toast.success('Albarán eliminado!');

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
