<template>
    <div>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goBack()"
                >
                    <v-icon color="primary">arrow_back</v-icon>
                </v-btn>
            </template>
                <span>Volver</span>
        </v-tooltip>
    </div>
</template>
<script>
export default {
    data () {
      return {
      }
    },
    methods:{
        goBack(){
           this.$router.go(-1)
        },
    }
}
</script>
