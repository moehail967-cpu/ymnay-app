<template>
    <!-- Payment Popup -->
    <div class="popup-fixed payment-popup">
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
                    <i class="las la-cash-register"></i>
                </div>
                <div>
                    <h5 class="font-bold text-sm text-gray-900 m-0">Payment</h5>
                    <p class="text-xs text-gray-400 m-0">Select a payment method to proceed</p>
                </div>
            </div>

            <!-- Body -->
            <div class="p-5">
                <div class="paymentMethod">
                    <!-- Payment method tabs -->
                    <ul class="paymentMethod__card tabs">
                        <li @click="handleTabs('cash'); handleGateway('cash')"
                            v-if="credentials.pos_payment_gateway_enable == 1"
                            class="paymentMethod__card__item cash single_click"
                            data-name="cash"
                            :class="activeInactiveTab('cash')">
                            <p class="paymentMethod__card__name" data-tab="cash">
                                <span class="icon"><i class="las la-money-bill-wave"></i></span>
                                Cash
                            </p>
                        </li>
                        <li @click="handleTabs('cards'); handleGateway('cards')"
                            v-if="credentials.pos_card_payment_gateway_enable == 1"
                            class="paymentMethod__card__item card single_click"
                            data-name="cards"
                            :class="activeInactiveTab('cards')">
                            <p class="paymentMethod__card__name" data-tab="cards">
                                <span class="icon"><i class="las la-credit-card"></i></span>
                                Card
                            </p>
                        </li>
                    </ul>

                    <!-- Cash Tab -->
                    <div class="tab_content_item" :class="activeInactiveTab('cash')" id="cash">
                        <div class="paymentMethod__wrap">
                            <SelectedCustomer :customer="customer"/>
                            <div class="paymentMethod__price mt-4">
                                <p class="paymentMethod__price__para">Order Total</p>
                                <p class="paymentMethod__price__title">{{ getCurrencySymbolWithAmount(totalAmount) }}</p>
                            </div>
                            <div class="paymentMethod__cash mt-4">
                                <div class="paymentMethod__cash__paid">
                                    <p class="paymentMethod__cash__para">Amount paid by customer</p>
                                    <div class="paymentMethod__cash__input">
                                        <input type="text" class="customer-paid form--control" value="0" @keyup="handleCustomerPaid($event)">
                                        <span class="paymentMethod__cash__input__sign"><i class="material-symbols-outlined"></i></span>
                                    </div>
                                </div>
                                <div class="paymentMethod__cash__return mt-4">
                                    <p class="paymentMethod__cash__return__para">Change to return</p>
                                    <h4 class="paymentMethod__cash__return__price">{{ getCurrencySymbolWithAmount(changeAmount.toFixed(2)) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Tab -->
                    <div class="tab_content_item" :class="activeInactiveTab('cards')" id="cards">
                        <div class="paymentMethod__wrap">
                            <SelectedCustomer :customer="customer" />
                            <div class="paymentMethod__price mt-4">
                                <p class="paymentMethod__price__para">Order Total</p>
                                <p class="paymentMethod__price__title">{{ getCurrencySymbolWithAmount(totalAmount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Send email toggle -->
                    <div class="send-email-toggle mt-4">
                        <label class="toggle-label" for="send_customer_email">
                            <input type="checkbox" @change="sendCustomerEmail($event)" id="send_customer_email" class="form-check" />
                            <span>Send receipt to customer email</span>
                        </label>
                        <p class="toggle-hint">A customer must be selected to receive the email.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                <form @submit="handleSubmit($event)" class="flex items-center justify-end gap-2">
                    <input type="hidden" name="selected_gateway" :value="activePaymentTab" id="selected_gateway"/>
                    <input type="hidden" name="selected_customer" value="" id="selected_customer"/>
                    <input type="hidden" name="coupon" value="" id="form_coupon" />
                    <input type="hidden" name="send_email" value="" id="form_send_email" />

                    <button id="close-proceed-to-cart" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all cursor-pointer"
                            @click="closePopup">
                        Cancel
                    </button>
                    <button id="submit-proceed-to-cart-btn" type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all cursor-pointer disabled:opacity-50"
                            style="background: var(--main-color-one);"
                            v-bind:disabled="disableSubmit">
                        <i class="las la-check-circle"></i> Pay Now
                        <SubmitButtonLoader v-show="buttonLoader"/>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="popup-overlay payment-popup-overlay" @click="closePopup"></div>
</template>

<script>
import axios from "axios";
import {onUpdated, reactive, ref, watch} from "vue";
import SelectedCustomer from "../customer/SelectedCustomer.vue";
import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
import InvoiceComponent from "../InvoiceComponent.vue";

export default {
    name: "ProceedToCart",
    components: {InvoiceComponent, SubmitButtonLoader, SelectedCustomer},
    props: {
        totalAmount: 0,
        customer: null
    },
    setup(props, {emit}){
        const activePaymentTab = ref('cash');
        const invoiceData = reactive({});
        const buttonLoader = ref(false);
        const customerPaidAmount = ref(0);
        const disableSubmit = ref(false);
        const totalAmount = ref();
        const changeAmount = ref(0);
        totalAmount.value = props.totalAmount;

        const credentials = ref({
            pos_payment_gateway_enable: false,
            pos_card_payment_gateway_enable: false,
            pos_e_wallet_payment_gateway_enable: false,
        });

        watch(() => props.totalAmount, (newValue, oldValue) => {
            totalAmount.value = newValue;
        });

        axios.get(window.appUrl + "/admin-home/pos/gateway-settings").then((response) => {
            credentials.value = response.data;
        }).catch((errors)=>{

        });

        function closePopup() {
            document.querySelector('.payment-popup')?.classList.remove('popup-active');
            document.querySelector('.payment-popup-overlay')?.classList.remove('popup-active');
        }

        function handleGateway(val){
            // first need to remove all active class
            let paymentGateway = document.querySelectorAll('.single_click');
            paymentGateway.forEach(function (element, key){
              element.classList.remove("active");
            })

            document.querySelector('.single_click[data-name='+ val +']').classList.add('active');

            document.querySelector("#selected_gateway").value = val;
        }

        function handleTabs(selector){
            document.querySelector("#cash").classList.remove("active");
            document.querySelector("#cards").classList.remove("active");

            document.querySelector("#" + selector).classList.add('active');

            activePaymentTab.value = selector;
            customerPaidAmount.value = 0;
            changeAmount.value = 0;
            document.querySelector('.customer-paid').value = 0;
        }

        function sendCustomerEmail(event){
            if(event.currentTarget.checked){
                document.querySelector("#form_send_email").value = "on";
            }else {
                document.querySelector("#form_send_email").value = "off";
            }
        }

        function handleCustomerPaid(event){
            changeAmount.value = event.target.value - totalAmount.value;
            customerPaidAmount.value = event.target.value;
        }

        function handleSubmit(event){
            event.preventDefault();

            if (activePaymentTab.value === 'cash' && customerPaidAmount.value < 1)
            {
                toastr.error("You must enter a amount");
                return;
            }

            if((Math.round(totalAmount.value) > 0) == false){
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Please add some product in to carts for purchase',
                    showConfirmButton: false,
                    timer: 1500
                });

                return ;
            }

            if (activePaymentTab.value === 'cards')
            {
                customerPaidAmount.value = Math.round(totalAmount.value);
                changeAmount.value = 0;
            }

            disableSubmit.value = true;
            buttonLoader.value = true;

            axios.post(window.appUrl + "/admin-home/pos/order/submit", new FormData(event.target)).then((response) => {
                const isSuccess = response.data.type === 'success';

                if (isSuccess)
                {
                    emit('cartAdded');

                    invoiceData.info = response.data.order_details;
                    invoiceData.transaction = {
                        customerPaidAmount: customerPaidAmount.value,
                        changeAmount: changeAmount.value
                    }

                    document.querySelector('.paymentMethod__cash__input input').value = 0;
                    changeAmount.value = 0;
                    customerPaidAmount.value = 0;
                    activePaymentTab.value = 'cash';
                }

                Swal.fire({
                    position: 'top-end',
                    icon: response.data.type,
                    title: response.data.msg,
                    showConfirmButton: false,
                    timer: response.data.timer ?? 1000
                });

                closePopup();
                disableSubmit.value = false;
                buttonLoader.value = false;

                if (isSuccess) {
                    emit('invoiceData', invoiceData);
                }
            });


        }

        const activeInactiveTab = (type) => {
            return activePaymentTab.value === type ? 'active' : '';
        }

        return {
            credentials,
            closePopup,
            handleTabs,
            handleGateway,
            handleCustomerPaid,
            handleSubmit,
            changeAmount,
            sendCustomerEmail,
            disableSubmit,
            customerPaidAmount,
            buttonLoader,
            invoiceData,
            activePaymentTab,
            activeInactiveTab
        };
    }
}
</script>
