export default class ModalMessage
{
    modalMessage;
    modal;
    constructor(message) {
        this.modalMessage = document.getElementById("modalMessage");
        this.setModalMessage(message);
        this.modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
    }

    setModalMessage(message) {
        if (message) {
            this.modalMessage.innerHTML = message;
        }
    }

    showModal() {
        this.modal.show();
    }

    hideModal() {
        this.modal.hide();
    }
}