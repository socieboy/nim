<template>
    <div>
        <button type="button" @click="ping()">Ping</button>
    </div>
</template>
<script>
    export default{

        props:['from'],

        methods:{
            ping(){
                axios.post('/api/network-interfaces/' + this.from.name + '/ping').then(response => {
                    console.log(response.data)
                }).catch(({response}) => {
                    console.log(response.data)
                })
            }
        }

    }
</script>
