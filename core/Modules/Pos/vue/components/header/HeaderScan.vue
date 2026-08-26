<template>
    <div class="dashboard_posSystem__header__item__flex">
        <div class="dashboard_posSystem__header__scan">
            <div class="dashboard_posSystem__header__scan__flex">
                <div class="dashboard_posSystem__header__scan__input">
                    <i class="las la-search search-input-icon"></i>
                    <input id="search-sku" @keyup="searchProduct()" type="text" class="form--control"
                           v-bind:placeholder="searchType === 'sku' ? 'Scan or enter SKU...' : 'Search products...'">
                    <button @click="searchProduct" class="search_icon"><i class="las la-search"></i></button>
                </div>

                <div class="dashboard_posSystem__header__scan__code">
                    <a href="#" class="scan_btn scan_sku_btn" :class="searchType === 'sku' ? 'active' : ''" @click.prevent="handleProductSearch">
                        <span class="scan_icon"><i class="las la-barcode"></i></span>
                        <span class="scan_title">Scan Code</span>
                    </a>
                    <a href="#" class="scan_btn reset_btn" @click.prevent="resetSearch">
                        <span class="scan_icon"><i class="las la-undo-alt"></i></span>
                        <span class="scan_title">Reset</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import RightCategory from "./RightCategory.vue";
  import {ref} from "vue";

  export default {
    name: "HeaderScan",
    components: {RightCategory},
    setup(props, {emit}){
        const searchType = ref('name');
      function searchProduct(){
          let key = document.querySelector("#search-sku").value;

          if (searchType.value === "sku")
          {
              emit("emitSearch", "sku=" + key);
          } else {
              emit("emitSearch", "name=" + key);
          }
      }

      function resetSearch(){
          emit("emitSearch","reset=true");
          searchType.value = 'name';
          document.querySelector("#search-sku").value = "";
      }

      function handleProductSearch(){
          document.getElementById("search-sku").focus();
          searchType.value = 'sku';
      }

      return {
          searchProduct,
          resetSearch,
          handleProductSearch,
          searchType
      };
    }
  }
</script>

