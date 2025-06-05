<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('product-type-select');
    const deliveryField = document.getElementById('delivery-time-field');
    const deliveryGroup = deliveryField ? deliveryField.closest('.col-md-6') : null;

    if (typeSelect && deliveryGroup) {
        toggleDeliveryField(typeSelect.value);

        typeSelect.addEventListener('change', function() {
            toggleDeliveryField(this.value);
        });

        function toggleDeliveryField(type) {
            if (type === 'preorder') {
                deliveryGroup.style.display = 'block';
            } else {
                deliveryGroup.style.display = 'none';
                if (deliveryField) {
                    deliveryField.value = 0;
                }
            }
        }
    }
});
</script>