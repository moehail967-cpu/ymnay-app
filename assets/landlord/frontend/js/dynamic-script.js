<script>
document.addEventListener("DOMContentLoaded", function() {
    // Find all style tags on the page
    const styles = document.querySelectorAll('style');
    
    styles.forEach(style => {
        // If the style tag contains the Shorooq font override, delete it
        if (style.innerHTML.includes('"Shorooq", sans-serif !important')) {
            style.remove();
            console.log("Successfully removed stuck Shorooq styling!");
        }
    });
});
</script>