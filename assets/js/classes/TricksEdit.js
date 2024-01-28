export default class TricksEdit {
    deleteHeroImageButton;
    InputHeroBackgroundImage;
    hero;
    functionNameToExecute;

    constructor(modalMessage) {
        this.modalYesButton = document.getElementById("modalYesButton");

        this.deleteHeroImageButton = document.getElementById("deleteHeroImageButton");
        this.hero = document.getElementById("hero");
        this.InputHeroBackgroundImage = document.getElementById("InputHeroBackgroundImage");
        modalMessage.setModalMessage("Confirmez-vous la suppression de l'image ?");
        
        // Adding listeners
        this.modalYesButton.addEventListener("click", this.executeFunction.bind(this));
        this.deleteHeroImageButton.addEventListener("click", this.setFunctionNameToExecute.bind(this));
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

    deleteHeroImage() {
        this.hero.style.backgroundImage = "";
        this.InputHeroBackgroundImage.value = "";
    }
}