<template>
    <button type="button" class="btn" :class="{
                    'btn-success': response === true,
                    'btn-outline-dark': response === null,
                    'btn-danger': response === false
                }" :disable="submitted" @click="ping()">
        <i class="fa fa-check" v-if="response"></i>
        <i class="fa fa-times" v-if="response != null && response === false"></i>
        <i class="fa fa-spin fa-spinner" v-if="submitted"></i>
        <span v-text="submitted ? 'Wait...' : 'Test Connection'"></span>
    </button>
</template>
<script>
    export default{

        props:['from'],

        data(){
            return {
                submitted: false,
                response: null,
            }
        },

        methods:{
            ping(){
                this.submitted = true;
                axios.post('/api/network-interfaces/' + this.from.name + '/ping').then(response => {
                    this.submitted = false;
                    this.response = response.data.status;
                }).catch(({response}) => {
                    this.submitted = false;
                })
            }
        }

    }
</script>
