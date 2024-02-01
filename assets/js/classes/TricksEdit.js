export default class TricksEdit {
    deleteHeroImageButton;
    InputHeroBackgroundImage;
    hero;
    functionNameToExecute;
    modal;
    mediaList;

    constructor(modalDialog) {
        this.modalYesButton = document.getElementById("modalYesButton");

        this.deleteHeroImageButton = document.getElementById("deleteHeroImageButton");
        this.hero = document.getElementById("hero");
        this.InputHeroBackgroundImage = document.getElementById("InputHeroBackgroundImage");
        this.modal = modalDialog; 
        this.modal.setModalMessage("Confirmez-vous la suppression de l'image ?");
        this.imagePath = document.getElementById("tricksImage");
        
        this.seeMediasButton = document.getElementById("seeMediasButton");
        this.mediaList = document.getElementById("mediaList");

        // Adding listeners
        this.modalYesButton.addEventListener("click", this.executeFunction.bind(this));
        this.deleteHeroImageButton.addEventListener("click", this.setFunctionNameToExecute.bind(this));
        this.imagePath.addEventListener("change", this.displayImageTricks.bind(this));
        this.seeMediasButton.addEventListener("click", this.showTricks.bind(this));
    }

    /**
     * Get the js function name from the dom
     */
    setFunctionNameToExecute() {
        this.functionNameToExecute = this.deleteHeroImageButton.getAttribute("data-function");
    }

    executeFunction() {
        // Checks if the function name exists in the class or globally
        if (typeof this[this.functionNameToExecute] === "function") {
            // Execute the function
            this[this.functionNameToExecute].call(this); 
        } else {
            console.error("La fonction " + this.functionNameToExecute + " n'existe pas ou n'est pas une fonction valide.");
        }
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
        this.hero.style.backgroundImage = "";
        this.modal.hideModal();
    }

    /**
     * Show the list of tricks on mobile devices
     */
    showTricks() {
        this.mediaList.classList.remove("d-none");
    }
}