document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.counter-plus').forEach(button => {
        button.addEventListener('click', (e) => {
            const qtyInput = e.target.closest('.counter').querySelector('#qty');
            let qtyValue = parseInt(qtyInput.value, 10) || 0;
            qtyInput.value = Math.min(qtyValue + 1, 99); // Limite supérieure : 99
        });
    });

    document.querySelectorAll('.counter-moins').forEach(button => {
        button.addEventListener('click', (e) => {
            const qtyInput = e.target.closest('.counter').querySelector('#qty');
            let qtyValue = parseInt(qtyInput.value, 10) || 0;
            qtyInput.value = Math.max(qtyValue - 1, 0); // Limite inférieure : 0
        });
    });
});
