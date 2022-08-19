<template>
    <div>
        <div class="card-header">Dedicated Server Details</div>
        <div class="card-body">
            <span>Server name: </span><span v-text="name"></span>
            <br>
            <span>Has no intervention?: </span><span v-text="noIntervention"></span>
            <br>
            <span>Rescue mail: </span><span v-text="rescueMail"></span>
            <br>
            <span>Datacenter: </span><span v-text="datacenter"></span>
            <br>
            <span>Rack: </span><span v-text="rack"></span>
            <br>
            <span>OS: </span><span v-text="os"></span>
            <br>
            <span>Reverse address: </span><span v-text="reverse"></span>
            <br>
            <span>Monitoring: </span><span v-text="monitoring"></span>
            <br>
            <span>State: </span><span v-text="state"></span>
            <br>
            <span>Server ID: </span><span v-text="serverId"></span>
            <br>
            <span>IP: </span><span v-text="ip"></span>
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        data() {
            return {
                name: "Loading...",
                noIntervention: "Loading...",
                rescueMail: "Loading...",
                datacenter: "Loading...",
                rack: "Loading...",
                os: "Loading...",
                reverse: "Loading...",
                monitoring: "Loading...",
                state: "Loading",
                serverId: "Loading...",
                ip: "Loading..."
            };
        },
        mounted() {
            console.log(axios.get("/api/dedicated/server/ssssss").then((response) => {
                this.name = response.data.name
                this.noIntervention = response.data.noIntervention
                this.rescueMail = response.data.rescueMail
                this.datacenter = response.data.datacenter
                this.rack = response.data.rack
                this.os = response.data.os
                this.reverse = response.data.reverse
                this.monitoring = response.data.monitoring
                this.state = response.data.state
                this.serverId = response.data.serverId
                this.ip = response.data.ip
            }));
        },
        methods: {

        },
        created() {

        }
    };
</script>

<style>
</style>
