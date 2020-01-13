<template>
    <div>

            <v-container>
                <h3>Permisos</h3>
                <p>{{permisos_selected}}</p>
                <v-layout row wrap>
                    <v-flex sm2
                        v-for="item in permisos"
                        :key="item"
                    >
                        <v-switch
                            v-on:change="setRolePermisos"
                            v-model="permisos_selected"
                            :label="item"
                            :value="item"
                            color="primary">
                        ></v-switch>
                    </v-flex>
                </v-layout>

            </v-container>

    </div>
</template>
<script>

export default {
    props:['role_id','permiso_role'],
    data () {
        return {
            permisos: [],
            permisos_selected: [],
        }
    },
    mounted(){

            //cargamos todos los permisos disponibles
        axios.get('/admin/permissions')
            .then(res => {

                var permisos = [];

                res.data.permisos.forEach(function(element) {
                    permisos.push(element.name);
                });
                this.permisos = permisos;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'users'})
            })
        this.permisos_selected = this.permiso_role;

    },
    computed: {
        setPerRole: {
            // getter
            get: function () {
                return this.permiso_role
            },
            // setter
            set: function (newValue) {

                this.permiso_role = newValue
            }
        }
    },
    methods:{
                //actualizamos role x usuario
        setRolePermisos(){
            this.setPerRole = this.permisos_selected;
            // axios({
            //     method: 'put',
            //     url: '/admin/users/'+this.user_id+'/roles',
            //     data:
            //         {
            //             roles: this.role_selected
            //         }
            //     })
            //     .then(response => {

            //         this.$toast.success(response.data);
            //     })
            //     .catch(err => {

            //         const msg_valid = err.response.data.errors;
            //         for (const prop in msg_valid) {
            //             this.$toast.error(`${msg_valid[prop]}`);
            //         }
            //     });
        }
    }
}
</script>

