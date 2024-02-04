export default class TricksEdit {

    deleteButtons;

    constructor(modalDialog) {
        this.deleteButtons = document.querySelectorAll('.delete-hero-image, .delete-media');

        this.deleteButtons.forEach((button) => {
            button.addEventListener('click', () => {

                modalDialog.setModalMessage("Confirmez-vous la suppression de l'image ?");
                modalDialog.showModal();

                document.querySelector('#modalYesButton').addEventListener('click', () => {
                    modalDialog.hideModal();

                    if (button.classList.contains('delete-hero-image')) {
                        this.deleteHeroImage();
                    } else if (button.classList.contains('delete-media')) {
                        const mediaId = button.getAttribute('data-media');
                        this.deleteMedia(mediaId);
                    } else if (button.classList.contains('delete-tricks')) {
                        this.deleteTricks();
                    }
                });
            });
        });
    }

    /**
     * Display the uploaded tricks image in the hero section
     * @param {*} event 
     */
    displayImageTricks (event) {
        // e.files contient un objet FileList
        const [picture] = event.target.files;
    
        // "picture" est un objet File
        if (picture) {
          // On change l"URL de l"image
          this.hero.style.backgroundImage = `url(${URL.createObjectURL(picture)})`;
        }
    }

    /**
     * Deletes the hero image
     */
    deleteHeroImage() {
        const hero = document.getElementById("hero");
        hero.style.backgroundImage = "";
    }

    /**
     * Deletes a Media
     */
    deleteMedia(mediaId) {
        const media = document.getElementById("media" + mediaId);
        const formTricks = document.getElementById("formTricks");
        var input = document.createElement('input');
        input.setAttribute('name', 'deleteMedia[]');
        input.setAttribute('id', 'deleteMedia[]');
        input.setAttribute('value', mediaId);
        input.setAttribute('type', 'hidden');
        input.setAttribute('form', 'formTricks');
        formTricks.appendChild(input);
        media.classList.add("d-none");
    }

    /**
     * 
     */
    deleteTricks() {

    }

    /**
     * Show the list of tricks on mobile devices
     */
    showTricks() {
        this.mediaList.classList.remove("d-none");
    }
}