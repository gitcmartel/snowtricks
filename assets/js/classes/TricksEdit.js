export default class TricksEdit {
    deleteHeroImageButton;
    InputHeroBackgroundImage;
    hero;
    functionNameToExecute;
    modal;

    constructor(modalDialog) {
        this.modalYesButton = document.getElementById("modalYesButton");

        this.deleteHeroImageButton = document.getElementById("deleteHeroImageButton");
        this.hero = document.getElementById("hero");
        this.InputHeroBackgroundImage = document.getElementById("InputHeroBackgroundImage");
        this.modal = modalDialog;
        this.modal.setModalMessage("Confirmez-vous la suppression de l'image ?");
        this.imagePath = document.getElementById("imagePath");
        
        // Adding listeners
        this.modalYesButton.addEventListener("click", this.executeFunction.bind(this));
        this.deleteHeroImageButton.addEventListener("click", this.setFunctionNameToExecute.bind(this));
        this.imagePath.addEventListener("change", this.displayImageTricks.bind(this));
    }

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

    displayImageTricks (event) {
        // e.files contient un objet FileList
        const [picture] = event.target.files;
    
        // "picture" est un objet File
        if (picture) {
          // On change l"URL de l"image
          this.hero.style.backgroundImage = `url(${URL.createObjectURL(picture)})`;
        }
    }

    deleteHeroImage() {
        this.hero.style.backgroundImage = "";
        this.InputHeroBackgroundImage.value = "";
        this.modal.hideModal();
    }
}