export default class seeMediasButton 
{
    seeMediasButton;
    mediaList;

    constructor () {
        this.seeMediasButton = document.getElementById("seeMediasButton");
        this.mediaList = document.getElementById("mediaList");
        
        //Adding listener
        this.seeMediasButton.addEventListener("click", this.showMedias.bind(this));
    }

    /**
     * Show the list of medias on mobile devices
     */
    showMedias() {
        this.mediaList.classList.remove("d-none");
    }
}   