export default class Toast 
{
    constructor () {
        document.addEventListener('DOMContentLoaded', function () {
            var toastElement = document.getElementById('appToast')
            if (toastElement) {
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });
    }
}