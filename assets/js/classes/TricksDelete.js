export default class TricksDelete {
    constructor(modalDialog) {
        this.modalDialog = modalDialog;
        this.deleteTricksButtons = document.querySelectorAll('.delete-tricks-1');
        this.clickHandler = this.clickHandler.bind(this);
        this.attachEventListeners();
    }

    attachEventListeners() {
        this.deleteTricksButtons.forEach((button) => {
            button.addEventListener('click', this.clickHandler);
        });
    }

    clickHandler(event) {
        const button = event.currentTarget;
        this.tricksId = button.dataset.tricksId;
        this.deleteTricks();
    }

    deleteTricks() {
        this.modalDialog.setModalMessage("Do you really want to delete this trick?");
        this.modalDialog.showModal();

        document.querySelector('#modalYesButton').addEventListener('click', () => {
            this.modalDialog.hideModal();
            this.sendDeleteRequest();
        });
    }

    sendDeleteRequest() {
        $.ajax({
            url: '/tricks/' + this.tricksId + '/delete',
            type: 'DELETE',
            success: (response) => {
                alert(response.message);
                window.location.href = '/';
            },
            error: (xhr, status, error) => {
                alert('An error occurred while deleting the trick.');
            }
        });
    }
}
