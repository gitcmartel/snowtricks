export default class TricksEdit {

    deleteButtons;
    editButtons;
    deleteTricksButton;

    constructor(modalDialog) {
        this.deleteButtons = document.querySelectorAll('.delete-hero-image, .delete-media');
        this.editButtons = document.querySelectorAll('.edit-hero-image, .edit-media');
        this.deleteTricksButton = document.getElementById('deleteTricks');
        
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
                    this.editMedia.bind(this)(event);
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

        this.deleteTricksButton.addEventListener('click', () => {

            modalDialog.setModalMessage("Confirmez-vous la suppression du tricks ?");
            modalDialog.showModal();

            document.querySelector('#modalYesButton').addEventListener('click', () => {
                modalDialog.hideModal();
                var tricksId = document.getElementById('tricks_form_id').value;

                $.ajax({
                    url: '/tricks/' + tricksId + '/delete',
                    type: 'DELETE',
                    success: function(response) {
                        // Gérer la réponse du serveur
                        alert(response.message);
                        window.location.href = '/';
                    },
                    error: function(xhr, status, error) {
                        // Gérer les erreurs
                        alert('Une erreur s\'est produite lors de la suppression du trick.');
                    }
                });
            });
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
     * Deletes the hero image
     */
    deleteHeroImage() {
        const hero = document.getElementById("hero");
        const isHeroImageDeleted = document.getElementById('tricks_form_isHeroImageDeleted');
        isHeroImageDeleted.checked = true;
        hero.style.backgroundImage = "";
    }

    /**
     * Modify a media's image
     */
    editMedia (event) {
        const [media] = event.target.files;
        const image = document.getElementById("image-" + event.target.id);
        const video = document.getElementById("video-" + event.target.id);
        const parentElement = document.getElementById("media-" + event.target.id);

        //We delete the content of mediaContainer
        if (parentElement) {
            // Tant qu'il y a des enfants, les supprimer un par un
            while (parentElement.firstChild) {
                parentElement.removeChild(parentElement.firstChild);
            }
        }

        if (media) {
            if (media.type.startsWith('image')) {
                const newImage = document.createElement('img');
                newImage.setAttribute('src', URL.createObjectURL(media));
                newImage.setAttribute('id', "image-" + event.target.id);
                parentElement.appendChild(newImage);
            } else if (media.type.startsWith('video')) {
                const newVideo = document.createElement('video');
                newVideo.setAttribute('src', URL.createObjectURL(media));
                newVideo.setAttribute('controls', '');
                const newSource = document.createElement('source');
                newSource.setAttribute('src', URL.createObjectURL(media));
                newSource.setAttribute('type', 'video/mp4');
                newSource.setAttribute('id', "video-" + event.target.id);
                newVideo.appendChild(newSource);
                parentElement.appendChild(newVideo);
            } 
        }
    }

    /**
     * Deletes a Media
     */
    deleteMedia(mediaId) {
        const media = document.getElementById("media" + mediaId);
        media.remove();
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