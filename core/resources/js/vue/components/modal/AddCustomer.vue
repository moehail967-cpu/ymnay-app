<template>
    <!-- Add Customer Popup -->
    <div class="popup-fixed add-customer-popup">
        <div class="bg-white rounded-2xl overflow-hidden relative" style="max-height: calc(100vh - 60px); overflow-y: auto; box-shadow: 0 24px 64px -12px rgba(17,24,39,.22);">
            <!-- Close -->
            <button type="button"
                    class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 text-gray-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all z-10 cursor-pointer"
                    @click="closePopup">
                <i class="las la-times text-sm"></i>
            </button>

            <!-- Header -->
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100" style="background: #f8fffe;">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                     style="background: rgba(var(--main-color-one-rgb),.1); color: var(--main-color-one);">
                    <i class="las la-user-plus"></i>
                </div>
                <div>
                    <h5 class="font-bold text-sm text-gray-900 m-0">Add New Customer</h5>
                    <p class="text-xs text-gray-400 m-0">Fill in the details to create a customer account</p>
                </div>
            </div>

            <!-- Body -->
            <div class="p-5">
                <form method="post" @submit.prevent.self="addCustomer($event)">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label for="firstName" class="text-xs font-bold text-gray-500 uppercase tracking-wide">Full Name</label>
                            <input type="text" name="name" id="firstName"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all"
                                   placeholder="Enter full name">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label for="PhoneNumber" class="text-xs font-bold text-gray-500 uppercase tracking-wide">Phone Number</label>
                            <input name="phone" type="tel" id="PhoneNumber"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all"
                                   placeholder="Enter phone number">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label for="emailAddress" class="text-xs font-bold text-gray-500 uppercase tracking-wide">Email Address</label>
                            <input name="email" type="email" id="emailAddress"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all"
                                   placeholder="Enter email address">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label for="username" class="text-xs font-bold text-gray-500 uppercase tracking-wide">Username</label>
                            <input name="username" @change="checkUsernameIsAvailableOrNot($event)"
                                   type="text" id="username"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all"
                                   placeholder="Enter username">
                            <p v-if="isAvailableUsername"
                               class="text-xs font-semibold mt-0.5"
                               :class="isAvailableUsername.type === 'success' ? 'text-green-600' : 'text-red-500'">
                                {{ isAvailableUsername.msg }}
                            </p>
                        </div>
                        <div class="col-span-2 flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Country</label>
                            <select name="country" @change="handleCountryState($event)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all">
                                <option value="">Select a country</option>
                                <option v-for="country in countries" v-bind:value="country.id" :key="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">State</label>
                            <select name="state" @change="handleStateCity($event)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all">
                                <option value="">Select a state</option>
                                <option v-for="state in states" v-bind:value="state.id" :key="state.id">
                                    {{ state.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">City</label>
                            <select name="city"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all">
                                <option value="">Select a city</option>
                                <option v-for="city in cities" v-bind:value="city.id" :key="city.id">
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-span-2 flex flex-col gap-1">
                            <label for="customerAddress" class="text-xs font-bold text-gray-500 uppercase tracking-wide">Address</label>
                            <input name="address" type="text" id="customerAddress"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-primary transition-all"
                                   placeholder="Enter customer address">
                        </div>
                    </div>

                    <!-- Footer buttons inside form -->
                    <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-gray-100">
                        <button id="add-customer-close-modal-button" type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all cursor-pointer"
                                @click="closePopup">
                            Cancel
                        </button>
                        <button id="add-customer-submit-modal-button" type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all cursor-pointer disabled:opacity-50"
                                style="background: var(--main-color-one);">
                            <i class="las la-user-plus"></i> Add Customer
                            <SubmitButtonLoader v-show="status"/>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="popup-overlay add-customer-popup-overlay" @click="closePopup"></div>
</template>

<script setup>
import axios from "axios";
import {ref} from "vue";
import Swal from "sweetalert2";
import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";

const status = ref(false);
const countries = ref({});
const states = ref({});
const cities = ref({});
const isAvailableUsername = ref({});

axios.get(window.appUrl + "/api/tenant/v1/get-countries").then((response) => {
    countries.value = response.data.countries;
});

function closePopup() {
    document.querySelector('.add-customer-popup')?.classList.remove('popup-active');
    document.querySelector('.add-customer-popup-overlay')?.classList.remove('popup-active');
}

function addCustomer(event) {
    let submitButton = document.querySelector("#add-customer-submit-modal-button");

    submitButton.setAttribute("disabled", true);
    status.value = !status.value;

    axios.post(window.appUrl + "/admin-home/pos/customer/add", new FormData(event.target)).then((response) => {
        submitButton.removeAttribute("disabled");

        Swal.fire({
            position: 'top-end',
            icon: response.data.type,
            title: response.data.msg,
            showConfirmButton: false,
            timer: 1500
        });

        closePopup();
        event.target.reset();
    }).catch((error) => {
        submitButton.removeAttribute("disabled");

        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach((field) => {
                errors[field].forEach((message) => {
                    toastr.error(message);
                });
            });
        }

        status.value = !status.value;
    });
}

function handleCountryState(event) {
    let country_id = event.target.value;
    if (country_id !== '') {
        axios.get(window.appUrl + "/api/tenant/v1/get-states/" + country_id).then((response) => {
            states.value = response.data.state;
        });
    } else {
        states.value = {};
    }
}

function handleStateCity(event) {
    let state_id = event.target.value;
    if (state_id !== '') {
        axios.get(window.appUrl + "/api/tenant/v1/get-cities/" + state_id).then((response) => {
            cities.value = response.data.cities;
        });
    } else {
        cities.value = {};
    }
}

function checkUsernameIsAvailableOrNot(event) {
    let username = event.target.value;
    if (username === '') {
        isAvailableUsername.value = {};
        return;
    }
    axios.post(window.appUrl + "/api/tenant/v1/username", {
        username: username
    }).then((response) => {
        isAvailableUsername.value = response.data;
    }).catch(() => {});
}
</script>
