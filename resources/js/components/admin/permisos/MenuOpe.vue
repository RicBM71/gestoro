<template>
    <div>
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
    </div>
</template>
<script>
export default {
    props:{
        id: Number
    },
    data () {
      return {
          dialog: false,
      }
    },
    methods:{
        goCreate(){
            this.$router.push({ name: 'permisos.create' })
        },
        goIndex(){
            this.$router.push({ name: 'permisos.index' })
        },
    }
}
</script>
