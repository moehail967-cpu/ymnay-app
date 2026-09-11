<script setup>
import {onMounted, ref, watch} from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const emits = defineEmits(null)
const cartTotalAmount = defineProps(['cartTotalAmount'])
const holdOrders = ref([]);
const showOrderList = ref(false);
const holdOrderButton = ref(false);

const getHoldCartItems = () => {
    getOnlyCartItems();
    showOrderList.value = !showOrderList.value;
}

const getOnlyCartItems = () => {
    axios.get(window.appUrl + "/admin-home/pos/get-hold-order").then((response) => {
        holdOrders.value = response.data;
        holdOrderButton.value = response.data.length > 0;
    }).catch((errors) => {
        prepare_errors(errors)
    });
}

const deleteHoldOrder = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert those!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a5c4e',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(window.appUrl + `/admin-home/pos/delete-hold-order?id=${id}`).then((response) => {
                // emit('orderCanceled', true);
                Swal.fire(
                    'Deleted!',
                    'The saved cart items has been deleted.',
                    'success'
                )

                getOnlyCartItems();
            }).catch((errors) => {
                prepare_errors(errors)
            });
        }
    })
}

const restoreHoldOrder = (id) => {
    axios.get(window.appUrl + `/admin-home/pos/restore-hold-order?id=${id}`).then((response) => {
        if (response.data.type === 'success')
        {
            getHoldCartItems();
            emits('cartItemRestored');
        } else {
            Swal.fire(
                'Warning!',
                response.data.msg,
                'error'
            );
        }
    }).catch((errors) => {

    });
}

watch(() => cartTotalAmount.cartTotalAmount, (newValue, oldValue) => {
    if (newValue === 0)
    {
        getOnlyCartItems();
    }
});

onMounted(() => {
    getOnlyCartItems();
})
</script>

<template>
    <div id="addCustomerButton" v-show="holdOrderButton"
         :class="['dashboard_posSystem__sidebar__item hold-orders-toggle', {'active': showOrderList}]">
        <a href="#0" class="addCustomer" @click="getHoldCartItems">
            <i class="las la-list-ul"></i>
            Hold Orders
            <span class="hold-count-badge" v-if="holdOrders.length > 0">{{ holdOrders.length }}</span>
            <i :class="['hold-icon las ml-auto', {'la-angle-down': showOrderList, 'la-angle-right': !showOrderList}]"></i>
        </a>
    </div>

    <div class="dashboard_posSystem__sidebar__item hold-orders-list" v-if="showOrderList && holdOrderButton">
        <div class="hold-orders-header">
            <i class="las la-layer-group"></i>
            <span>Saved Orders</span>
        </div>
        <div class="hold-orders-body">
            <div class="hold-order-item" v-for="order in holdOrders" :key="order.name" v-if="holdOrders.length > 0">
                <span class="hold-order-index">{{ holdOrders.indexOf(order) + 1 }}</span>
                <span class="hold-order-name">{{ order.name }}</span>
                <div class="hold-order-actions">
                    <a class="tick-btn btn-info" @click.prevent="restoreHoldOrder(order.name)" title="Restore">
                        <i class="las la-check"></i>
                    </a>
                    <a class="tick-btn btn-danger" @click.prevent="deleteHoldOrder(order.name)" title="Delete">
                        <i class="las la-times"></i>
                    </a>
                </div>
            </div>
            <div class="hold-order-empty" v-else>
                <i class="las la-inbox"></i>
                <p>No orders saved yet</p>
            </div>
        </div>
    </div>
</template>
