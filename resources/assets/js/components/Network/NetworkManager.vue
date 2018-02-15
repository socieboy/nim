<template>
    <div class="network-manager">
        <ul class="nav nav-tabs" id="Tabs" role="tablist">
            <li class="nav-item" v-for="(interface, key, index) in interfaces">
                <a class="nav-link"
                   :class="{'active' : index == 0}"
                   :id="interface.name + '-tab'"
                   :href="'#' + interface.name"
                   :aria-controls="interface.name"
                   data-toggle="tab"
                   role="tab"
                   aria-selected="true">
                   {{ interface.mode + ' ' + interface.name }}
                </a>
            </li>
        </ul>
        <div class="tab-content" id="TabContent">
            <div class="tab-pane fade"
                 v-for="(interface, key, index) in interfaces"
                 :class="{' show active' : index == 0}"
                 :id="interface.name"
                 :aria-labelledby="interface.name + '-tab'"
                 role="tabpanel">
                <network-interface :interface="interface"></network-interface>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                interfaces: []
            }
        },

        created(){
            axios.get('/api/network-interfaces').then(response => {
                this.interfaces = response.data;
            });
        },

    }
</script>
