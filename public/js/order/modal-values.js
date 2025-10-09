document.querySelectorAll('.file-action-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const deliveryId = this.dataset.deliveryId;
        
        document.getElementById('selected-order-id').value = orderId;
        document.getElementById('selected-delivery-id').value = deliveryId;
        
        // Filter table rows to show only items for this delivery
        filterDeliveryItems(deliveryId);
    });
});