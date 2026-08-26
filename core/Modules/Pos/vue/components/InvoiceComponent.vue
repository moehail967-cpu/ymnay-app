<script setup>
    import {onMounted, ref} from "vue";

    const emits = defineEmits(['dataPrinted']);
    const props = defineProps(['invoice', 'directPrint'])
    let siteInfo = props.invoice.info.site_info;
    let cartInfo = props.invoice.cart;
    let transactionInfo = props.invoice.transaction;
    let pricing = props.invoice.pricing;

    const invoiceContent = ref(null);

    const printInvoice = () => {
        if (!invoiceContent.value) return;

        const printContent = invoiceContent.value.innerHTML;
        const shouldPrint = props.directPrint;
        const cssUrl = `${window.appUrl}/assets/tenant/backend/css/pos-invoice.css`;

        // Emit first so component unmounts cleanly before the window is opened
        emits('dataPrinted');

        const autoPrintScript = shouldPrint
            ? `<script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 150); });<\/script>`
            : '';

        const fullHtml = `<!DOCTYPE html><html><head><meta charset="UTF-8">
            <link rel="stylesheet" href="${cssUrl}">
            ${autoPrintScript}
        </head><body>${printContent}</body></html>`;

        try {
            const blob = new Blob([fullHtml], { type: 'text/html' });
            const blobUrl = URL.createObjectURL(blob);

            const newWin = window.open(blobUrl, '_blank');

            if (!newWin) {
                URL.revokeObjectURL(blobUrl);
                toastr.error('Please allow popups in your browser to view or print the receipt.');
                return;
            }

            newWin.addEventListener('load', () => {
                URL.revokeObjectURL(blobUrl);
            });

        } catch (e) {
            toastr.error('Could not open receipt. Please allow popups and try again.');
        }
    }

    onMounted(() => {
        printInvoice();
    })
</script>

<template>
    <div class="receipt-container invoice" ref="invoiceContent">
        <header class="receipt-header">
            <h1>Money Receipt</h1>
            <p><strong>{{ siteInfo.name }}</strong></p>
            <p><strong>Email:</strong> {{ siteInfo.email }}</p>
            <p><strong>Website:</strong> {{ siteInfo.website }}</p>
            <p><strong>Date:</strong> {{ props.invoice.info.date }}</p>
        </header>
        <div class="receipt-body">
            <table class="receipt-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in cartInfo" :key="item.name">
                    <td>{{ item.name }}</td>
                    <td>{{ item.qty }}</td>
                    <td>{{ getCurrencySymbolWithAmount(item.price) }}</td>
                    <td>{{ getCurrencySymbolWithAmount(item.subtotal) }}</td>
                </tr>
                </tbody>
            </table>
            <div class="receipt-summary">
                <p><strong>Subtotal:</strong> {{ getCurrencySymbolWithAmount(pricing.sub_total) }}</p>
                <p><strong>Tax ({{ pricing.tax }}%):</strong> {{ getCurrencySymbolWithAmount(pricing.taxed_amount.toFixed(2)) }}</p>
                <p><strong>Total:</strong> {{ getCurrencySymbolWithAmount(pricing.total) }}</p>
                <p><strong>Paid Amount:</strong> {{ getCurrencySymbolWithAmount(transactionInfo.customerPaidAmount) }}</p>
                <p><strong>Change Due:</strong> {{ getCurrencySymbolWithAmount(transactionInfo.changeAmount.toFixed(2)) }}</p>
            </div>
        </div>
        <footer class="receipt-footer">
            <p>Thank you for shopping with us!</p>
            <p>Visit our website: {{ siteInfo.website }}</p>
        </footer>
    </div>
</template>
