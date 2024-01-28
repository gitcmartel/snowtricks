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
        this.modalMessage.innerHTML = message;
    }

    hideModal() {
        this.modal.hide();
    }
}