<script setup>
    import {ref} from "vue";
    import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
    const emits = defineEmits(['invoiceDataStatus', 'directPrint']);
    const props = defineProps(['orderDetails']);

    const invoiceDataStatus = ref(false);
    const loaderButton = ref(false);

    const closePopup = () => {
        document.querySelector('.invoice-popup')?.classList.remove('popup-active');
        document.querySelector('.invoice-popup-overlay')?.classList.remove('popup-active');
    };

    const printReceipt = () => {
        loaderButton.value = true;
        invoiceDataStatus.value = true;
        emits('invoiceDataStatus', invoiceDataStatus.value);
        emits('directPrint');

        setTimeout(()=>{
            loaderButton.value = false;
        }, 500)
    }

    const showReceipt = () => {
        loaderButton.value = true;
        invoiceDataStatus.value = true;
        emits('invoiceDataStatus', invoiceDataStatus.value);

        setTimeout(()=>{
            loaderButton.value = false;
        }, 500)
    }

    const readOrderDetails = () => {
        let invoice_number = props.orderDetails.invoice_number;
        let url = window.appUrl + `/admin-home/order-manage/view/${invoice_number}`;

        window.open(url, '_blank', 'noreferrer');
    }
</script>

<template>
    <!-- Invoice Popup -->
    <div class="popup-fixed invoice-popup">
        <div class="bg-white rounded-2xl overflow-hidden relative" style="max-height: calc(100vh - 60px); overflow-y: auto; box-shadow: 0 24px 64px -12px rgba(17,24,39,.22);">
            <!-- Close -->
            <button type="button"
                    class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 text-gray-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all z-10 cursor-pointer"
                    @click="closePopup">
                <i class="las la-times text-sm"></i>
            </button>

            <!-- Header -->
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100" style="background: #f0fdf4;">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                     style="background: rgba(34,197,94,.15); color: #16a34a;">
                    <i class="las la-check-circle"></i>
                </div>
                <div>
                    <h5 class="font-bold text-sm text-gray-900 m-0">Order Complete</h5>
                    <p class="text-xs text-gray-400 m-0">The order has been placed successfully</p>
                </div>
            </div>

            <!-- Body -->
            <div class="p-5 text-center">
                <div class="order-success-icon">
                    <i class="las la-check-circle"></i>
                </div>
                <p class="order-success-text mt-3">
                    Order placed successfully! Choose what you'd like to do next.
                </p>
                <div class="invoice-action-buttons mt-4">
                    <button type="button" class="invoice-btn invoice-btn--details" @click.prevent="readOrderDetails">
                        <i class="las la-external-link-alt"></i> Order Details
                    </button>
                    <button type="button" class="invoice-btn invoice-btn--show" @click.prevent="showReceipt">
                        <i class="las la-eye"></i> Show Receipt
                        <SubmitButtonLoader v-if="loaderButton"/>
                    </button>
                    <button type="button" class="invoice-btn invoice-btn--print" @click.prevent="printReceipt">
                        <i class="las la-print"></i> Print Receipt
                        <SubmitButtonLoader v-if="loaderButton"/>
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all cursor-pointer"
                        @click="closePopup">
                    Close
                </button>
            </div>
        </div>
    </div>
    <div class="popup-overlay invoice-popup-overlay" @click="closePopup"></div>
</template>
