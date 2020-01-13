<template>
    <div>
        <v-layout row wrap>
            <v-layout row wrap text-xs-center>
                <v-flex sm12><h2>{{user.username}}</h2></v-flex>
            </v-layout>
        </v-layout>
        <v-layout row wrap>
            <v-flex sm3
                v-for="item in empresas_open"
                :key="item.id"
            >
                <v-switch
                    v-on:change="setUserEmp"
                    v-model="emp_selected"

                    :label="item.nombre"
                    :value="item.id"
                    color="warning">
                ></v-switch>
            </v-flex>
        </v-layout>
    </div>
</template>
<script>
export default {
    props: ['user','emp_user','empresas'],
    data () {
        return {
            emp_selected: [],
            show: false,
            empresas_open: [],
        }
    },
    mounted(){

        this.empresas.map((e) => {
            this.empresas_open.push({id: e.id, nombre: e.titulo});
        })
        this.show = (this.empresas_open.length > 0 );


            //cargamos todos las empresas disponibles
        // axios.get('/admin/empresas')
        //     .then(res => {

        //         var emps = [];

        //         res.data.map((e) => {
        //             this.empresas.push({id: e.id, nombre: e.titulo});
        //         })

        //         this.show = (this.empresas.length > 0 );
        //     })
        //     .catch(err => {
        //         this.$toast.error(err.response.data.message);
        //         this.$router.push({ name: 'users'})
        //     })

        this.emp_selected = this.emp_user;

    },
    methods:{
        setUserEmp(){

            axios({
                url: '/admin/users/'+this.user.id+'/empresas',
                method: 'put',
                data:
                    {
                        empresas: this.emp_selected,
                        seleccionadas: this.emp_selected.length
                    }
                })
                .then(res => {
                    this.$toast.success("Empresa asignada correctamente");
                    //this.setEmpresaDef();
                    })
                .catch(err => {

                    this.$toast.error("Error al asignar empresa");
                });
        },
        // setEmpresaDef(){
        //     axios.put('/admin/users/'+this.user.id+'/empresa',{ empresa: this.emp_selected[0] })
        //     .then(res => {
        //             this.$toast.success("Empresa asignada correctamente");
        //             })
        //         .catch(err => {
        //             this.$toast.error("Error al establecer empresa x defecto");
        //         });
        // }
    }
}
</script>
