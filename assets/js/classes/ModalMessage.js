export default class ModalMessage
{
    modalMessage;
    constructor(message) {
        this.modalMessage = document.getElementById("modalMessage");
        this.setModalMessage(message);
    }

    setModalMessage(message) {
        this.modalMessage.innerHTML = message;
    }
}