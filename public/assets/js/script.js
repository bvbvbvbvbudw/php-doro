document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('sort').addEventListener('click', function () {
        var orderInput = document.getElementById('order-input');
        const sort = document.getElementById('sort');
        sort.addEventListener('click', function () {
            sort.classList.toggle('rotate');
        });

        if (orderInput.value === 'asc') {
            orderInput.value = 'desc';
        } else {
            orderInput.value = 'asc';
        }
        orderInput.form.submit();
    });
});