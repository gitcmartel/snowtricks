export default class TricksEdit {

    deleteButtons;
    editButtons;

    constructor(modalDialog) {
        this.deleteButtons = document.querySelectorAll('.delete-hero-image, .delete-media');
        this.editButtons = document.querySelectorAll('.edit-hero-image, .edit-media');

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

        this.editButtons.forEach((button) => {
            button.addEventListener('change', () => {
                if (button.classList.contains('edit-hero-image')) {
                    this.editImageTricks.bind(this)(event);
                } else if (button.classList.contains('edit-media')) {
                    this.editMedia.bind(this);
                }
            });
        
        });

        document
            .querySelectorAll('.add_item_link')
            .forEach(btn => {
                btn.addEventListener("click", this.addFormToCollection)
        });

        document
            .querySelectorAll('ul.tags li')
            .forEach((tag) => {
                this.addTagFormDeleteLink(tag)
        });
    }

    /**
     * Display the uploaded tricks image in the hero section
     * @param {*} event 
     */
    editImageTricks (event) {
        // e.files contient un objet FileList
        const [picture] = event.target.files;
        const hero = document.getElementById('hero');

        // "picture" est un objet File
        if (picture) {
          // On change l"URL de l"image
          hero.style.backgroundImage = `url(${URL.createObjectURL(picture)})`;
          //On repasse la valeur de l'input qui controle une eventuelle suppression a false
          const isHeroImageDeleted = document.getElementById('tricks_form_isHeroImageDeleted');
          isHeroImageDeleted.checked = false;
        }
    }

    /**
     * 
     */

    editMedia () {

    }

    /**
     * Deletes the hero image
     */
    deleteHeroImage() {
        const hero = document.getElementById("hero");
        const isHeroImageDeleted = document.getElementById('tricks_form_isHeroImageDeleted');
        isHeroImageDeleted.checked = true;
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

    addFormToCollection = (e) => {
        const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
      
        const item = document.createElement('li');
      
        item.innerHTML = collectionHolder
          .dataset
          .prototype
          .replace(
            /__name__/g,
            collectionHolder.dataset.index
          );
      
        collectionHolder.appendChild(item);
      
        collectionHolder.dataset.index++;

        // add a delete link to the new form
        this.addTagFormDeleteLink(item);
      };

      addTagFormDeleteLink = (item) => {
        const removeFormButton = document.createElement('button');
        removeFormButton.innerText = 'Delete this media';
    
        item.append(removeFormButton);
    
        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }
}