<script setup>
    import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
    import {ref} from "vue";
    import axios from "axios";
    import Swal from "sweetalert2";

    const props = defineProps(['cartProducts','cartTotalAmount']);
    const buttonLoader = ref(false);
    const currentDateTime = ref(null);
    const emit = defineEmits(['newCartProducts']);

    const getDateTime = () => {
        const now = new Date();
        const date = now.getDate();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();
        let hour = now.getHours();
        const minute = now.getMinutes();
        const second = now.getSeconds();
        const amPm = now.getHours() >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12;
        return `Cart-${date}-${month}-${year},${hour}:${minute}:${second}${amPm}`;
    };

    const closePopup = () => {
        document.querySelector('.hold-popup')?.classList.remove('popup-active');
        document.querySelector('.hold-popup-overlay')?.classList.remove('popup-active');
    };

    const submitHolderOrder = () => {
        buttonLoader.value = true;
        let cartName = currentDateTime.value ? currentDateTime.value.value : null;

        axios.post(window.appUrl + '/admin-home/pos/hold-order', {
            cart_name: cartName
        }).then((response) => {
            if (response.data.type === 'success') {
                Swal.fire({
                    position: 'top-end',
                    icon: response.data.type,
                    title: response.data.msg,
                    showConfirmButton: false,
                    timer: 1000
                });
                buttonLoader.value = false;
                closePopup();
                emit('newCartProducts', {});
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: response.data.type,
                    title: response.data.msg,
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        }).catch(() => {
            buttonLoader.value = false;
        });
    };
</script>

<template>
    <!-- Hold Order Popup -->
    <div class="popup-fixed hold-popup">
        <div class="popup-contents">
            <a href="#1" class="popup-contents-close" @click.prevent="closePopup">
                <i class="las la-times"></i>
            </a>

            <div class="popup_contents_header">
                <div class="popup_contents_header__flex">
                    <h5 class="popup_contents_title">Hold This Order</h5>
                </div>
                <p class="modal-subtitle">Save this order to resume later</p>
            </div>
            <hr/>

            <div class="popup-contents-interview">
                <div class="hold-cart-preview">
                    <div class="hold-cart-item" v-for="product in props.cartProducts" :key="product.rowId">
                        <p class="hold-cart-item__name">{{ product.name }} <span class="qty-badge">x{{ product.qty }}</span></p>
                        <p class="hold-cart-item__price">{{ getCurrencySymbolWithAmount(product.price) }}</p>
                    </div>
                </div>
                <div class="hold-cart-total">
                    <span>Subtotal</span>
                    <strong>{{ getCurrencySymbolWithAmount(props.cartTotalAmount.sub_total) }}</strong>
                </div>
                <div class="single-input mt-4">
                    <label for="cartSaveName" class="label_title">Cart Save Name</label>
                    <input id="cartSaveName" class="form--control" type="text" :value="getDateTime()" ref="currentDateTime">
                </div>
            </div>

            <div class="popup_contents_btn">
                <div class="btn-wrapper d-flex gap-3 justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" @click.prevent="closePopup">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" @click.prevent="submitHolderOrder">
                        <i class="las la-pause-circle"></i> Hold Order
                        <SubmitButtonLoader v-show="buttonLoader"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="popup-overlay hold-popup-overlay" @click="closePopup"></div>
</template>
