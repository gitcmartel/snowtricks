export default class TricksEdit {

    deleteButtons;
    editButtons;
    deleteTricksButton;
    radioImage;
    radioVideo;
    btnValidateEditMedia;
    

    constructor(modalDialog) {
        this.deleteButtons = document.querySelectorAll('.delete-hero-image, .delete-media');
        this.editButtons = document.querySelectorAll('.edit-hero-image, .edit-media');
        this.radioImage = document.querySelectorAll('.radio-image');
        this.radioVideo = document.querySelectorAll('.radio-video');
        this.btnValidateEditMedia = document.querySelectorAll('.btn-validate-edit-media');
        this.deleteTricksButton = document.getElementById('deleteTricks');
        

        // Text editor tinymce initialization
        /*
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: 'textarea.tinymce-editor'
            });
        });
        */
       
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
                }
            });
        
        });

        this.btnValidateEditMedia.forEach((button) => {
            button.addEventListener('click', () => {
                this.editMedia(button); 
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

        this.radioImage.forEach((radio) => {
            radio.addEventListener('change', () => {
                this.handleImageRadioChange(radio);
            });
        });

        this.radioVideo.forEach((radio) => {
            radio.addEventListener('change', () => {
                this.handleVideoRadioChange(radio);
            });
        });
    }

    handleVideoRadioChange(radio) {
        const imageElement = radio.getAttribute("data-image-element");
        const videoElement = radio.getAttribute("data-video-element");
        const video = document.getElementById(videoElement);
        const image = document.getElementById(imageElement);
    
        image.classList.add('d-none');
        video.classList.remove('d-none');
        image.querySelector("input").value = "";
    }

    handleImageRadioChange(radio) {
        const imageElement = radio.getAttribute("data-image-element");
        const videoElement = radio.getAttribute("data-video-element");
        const video = document.getElementById(videoElement);
        const image = document.getElementById(imageElement);
    
        image.classList.remove('d-none');
        video.classList.add('d-none');
        video.querySelector("input").value = "";
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
     * Modify a media's image or video
     */
    editMedia (button) {
        const newImagePath = document.getElementById(button.getAttribute("data-add-image-element")).querySelector("input");
        const newVideoPath = document.getElementById(button.getAttribute("data-add-video-element")).querySelector("input");
        const mediaType = document.getElementById(button.getAttribute("data-type-element"));
        const parentElement = document.getElementById(button.getAttribute("data-media-id"));

        //We delete the content of mediaContainer
        if (parentElement) {
            // Tant qu'il y a des enfants, les supprimer un par un
            while (parentElement.firstChild) {
                parentElement.removeChild(parentElement.firstChild);
            }
        }

        if (newImagePath.files.length > 0) {
            const newImage = document.createElement('img');
            newImage.setAttribute('src', URL.createObjectURL(newImagePath.files[0]));
            newImage.setAttribute('id', button.getAttribute("data-image-element"));
            parentElement.appendChild(newImage);
            mediaType.value = "image";
        }

        if (newVideoPath.value !== "") {
            const newVideo = document.createElement("iframe");
            newVideo.setAttribute("width", "100%");
            newVideo.setAttribute("height", "100%");
            newVideo.setAttribute("src", newVideoPath.value);
            newVideo.setAttribute("title", "YouTube video player");
            newVideo.setAttribute("frameborder", "0");
            newVideo.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share");
            newVideo.setAttribute("allowfullscreen", "");
            parentElement.appendChild(newVideo);
            mediaType.value = "video";
        } 
    }

    /**
     * Deletes a Media
     */
    deleteMedia(mediaId) {
        const media = document.getElementById("media" + mediaId);
        media.remove();
    }

    addFormToCollection = (e) => {
        const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
      
        const item = document.createElement('li');
        item.classList.add("list-group-item");
      
        item.innerHTML = collectionHolder
          .dataset
          .prototype
          .replace(
            /__media_name__/g,
            collectionHolder.dataset.index
        );
        
        const imageElementId = item.querySelector("input[type=file]").id;
        const videoElementId = item.querySelector("input[type=text]").id;

        const firstChild = item.firstChild;

        const radioButtons = this.createRadiosImageAndVideosElements(collectionHolder, imageElementId, videoElementId);
        item.insertBefore(radioButtons, firstChild);

        collectionHolder.appendChild(item);
      
        collectionHolder.dataset.index++;

        // add a delete link to the new form
        this.addTagFormDeleteLink(item);
      };

      addTagFormDeleteLink = (item) => {
        const removeFormButton = document.createElement('button');
        removeFormButton.innerHTML = '<i class="fa-solid fa-trash-alt fa-xl" style="color: red;"></i>';
        removeFormButton.classList.add('btn');
    
        item.append(removeFormButton);
    
        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

    createRadioElement(labelText, id, imageElementId, videoElementId, name, className, checked, changeHandler) {
        // Create element div.form-check
        var div = document.createElement("div");
        div.className = "form-check me-3";
    
        // Create input radio
        var input = document.createElement("input");
        input.className = "form-check-input me-2" + className;
        input.type = "radio";
        input.name = name;
        input.id = id;
        input.setAttribute("data-image-element", imageElementId);
        input.setAttribute("data-video-element", videoElementId);
        if (checked) {
            input.checked = true;
        }
        
        // Adding listener
        if (typeof changeHandler === 'function') {
            input.addEventListener('change', function() {
                changeHandler(input); 
            });
        }

        // Create label
        var label = document.createElement("label");
        label.className = "form-check-label";
        label.htmlFor = id;
        label.textContent = labelText;
    
        // Add input and label to the div element
        div.appendChild(input);
        div.appendChild(label);
    
        return div;
    }

    createRadiosImageAndVideosElements(collectionHolder, imageElementId, videoElementId) {
        // Video radio element
        const radioVideo = this.createRadioElement("Video link", "videoId-" + collectionHolder.dataset.index, 
        imageElementId, videoElementId, "media-" + collectionHolder.dataset.index, "radio-video", false, 
        this.handleVideoRadioChange);

        // Image radio element
        const radioImage = this.createRadioElement("Image", "imageId-" + collectionHolder.dataset.index, 
        imageElementId, videoElementId, "media-" + collectionHolder.dataset.index, "radio-image", true, 
        this.handleImageRadioChange);

        const div = document.createElement('div');
        div.classList.add('d-flex', 'my-3');
        div.appendChild(radioImage);
        div.appendChild(radioVideo);

        return div;
    }
}