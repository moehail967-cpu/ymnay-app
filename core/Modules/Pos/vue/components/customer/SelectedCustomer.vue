<template>
    <div class="selected-customer-card mt-3" v-if="customer != null">
        <div class="selected-customer-card__header">
            <span class="selected-customer-card__label">
                <i class="las la-user-check"></i> Selected Customer
            </span>
        </div>
        <div class="searchWrap__item__flex mt-2">
            <div class="searchWrap__left">
                <div class="searchWrap__left__flex">
                    <div class="searchWrap__left__thumb">
                        <img v-bind:src="customer.image" alt="profile">
                    </div>
                    <div class="searchWrap__left__contents">
                        <h5 class="searchWrap__left__contents__title">{{ customer.phone }}</h5>
                        <p class="searchWrap__left__contents__subtitle">{{ customer.name }}</p>
                    </div>
                </div>
            </div>
            <div class="searchWrap__right">
                <p class="text-muted-sm">Member since</p>
                <p class="text-xs-bold">{{ customer.date }}</p>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
      name: "SelectedCustomer",
      props: {
          customer: null,
      }
  }
</script>
