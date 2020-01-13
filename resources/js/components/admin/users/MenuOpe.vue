<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
         <v-dialog v-model="dialog_help" persistent max-width="800">
            <v-card>
                <v-card-title class="headline">Roles y Permisos</v-card-title>
                <v-card-text>
                    <v-list three-line>
                        <v-list-tile-content>
                            <v-list-tile-title>Administrador</v-list-tile-title>
                            <v-list-tile-sub-title>Acceso a datos de administración de la aplicación: empresas, libros, contadores, cuentas.</v-list-tile-sub-title>
                            <v-list-tile-sub-title>Un administrador podrá "saltarse" controles, tales como plazos de recuperación, ampliaciones, entregas a cuenta.</v-list-tile-sub-title>
                            <v-list-tile-title>Supervisor</v-list-tile-title>
                            <v-list-tile-sub-title>COMPRAS: Modifica fechas, Modifica importe ampliación, salto control recuperaciones, reabre lotes, borra operaciones</v-list-tile-sub-title>
                            <v-list-tile-sub-title>CLIENTES: Edita cliente sin restricciones</v-list-tile-sub-title>
                            <v-list-tile-sub-title>CAJA: Borra apuntes de caja - Autoriza y modifica traspasos</v-list-tile-sub-title>
                            <v-list-tile-title>Permisos</v-list-tile-title>
                            <v-list-tile-sub-title>Los permisos señalados con un asterisco (*) solo deberían activarse temporalmente para lleva a cabo la acción requerida. Finaliza conviene desactivarlo por motivos de seguridad en la gestión operativa.</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" round flat @click="dialog_help = false">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goCreate"
                >
                    <v-icon color="primary">add</v-icon>
                </v-btn>
            </template>
                <span>Nuevo</span>
        </v-tooltip>
         <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="openDialog"
                >
                    <v-icon color="primary">delete</v-icon>
                </v-btn>
            </template>
                <span>Borrar Registro</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="reset"
                >
                    <v-icon color="primary">cached</v-icon>
                </v-btn>
            </template>
                <span>Reset Password</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goIndex"
                >
                    <v-icon color="primary">list</v-icon>
                </v-btn>
            </template>
            <span>Lista</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="dialog_help=true"
                >
                    <v-icon color="primary">help_outline</v-icon>
                </v-btn>
            </template>
            <span>Ayuda</span>
        </v-tooltip>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
export default {
    props:{
        id: Number
    },
    components: {
        'my-dialog': MyDialog
    },
    data () {
      return {
          dialog: false,
          dialog_help: false,
      }
    },
    methods:{
        goCreate(){
            this.$router.push({ name: 'users.create' })
        },
        goIndex(){
            this.$router.push({ name: 'users.index' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/admin/users/'+this.id,{_method: 'delete'})
                .then(response => {
                this.$router.push({ name: 'users.index' })
                this.$toast.success('Usuario eliminada!');

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        reset(){
            if (confirm('¿Resetar password del usuario?')){
                axios({
                    method: 'put',
                    url: '/admin/users/'+this.id+'/reset',
                    })
                    .then(response => {
                        this.$toast.success(response.data.msg);
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data);
                    });
            }
        },
        goHelp(){
            this.dialog_help = true;
        }

    }
}
</script>
