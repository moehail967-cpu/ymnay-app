<script setup>
    import axios from "axios";
    import {onMounted, ref} from "vue";

    const location = ref(false);
    const locationSettingsUrl = ref("#");
    const hasLocation = () => {
        axios.get(window.appUrl + '/admin-home/pos/store-location')
            .then((response) => {
                location.value = response.data.location == null;
            }).catch((error) => {
                prepare_errors(error)
        });
    }

    const locationSettingsPage = () => {
        axios.get(window.appUrl + '/admin-home/pos/location-settings')
            .then((response) => {
                locationSettingsUrl.value = response.data;
            }).catch((error) => {
            prepare_errors(error)
        });
    }

    onMounted(() => {
        hasLocation();
        locationSettingsPage();
    });
</script>

<template>
    <div class="store-location-alert" v-if="location">
        <i class="las la-map-marker-alt"></i>
        <span>Please select your store location first.</span>
        <a :href="locationSettingsUrl">Configure now <i class="las la-arrow-right"></i></a>
    </div>
</template>
