import TricksDelete from "./TricksDelete.js";
import ModalMessage from "./ModalMessage.js";

export default class TricksScroll
{
    page;
    loading;
    pageContainer;
    spinner;
    overlay;
    stopLoadingTricks;
    TricksDelete

    constructor (tricksDelete)
    {
        this.page = 2;
        this.loading = false;
        this.pageContainer = document.getElementById("pages");
        this.overlay = document.getElementById("spinner-container");
        this.stopLoadingTricks = false;
        this.tricksDelete = tricksDelete;

        //Adding listeners
        window.addEventListener('scroll', this.windowScrollListener.bind(this));

    };

    windowScrollListener () {
        if ((window.innerHeight + window.scrollY) >= (this.pageContainer.offsetTop + this.pageContainer.offsetHeight + 130)) {
            if (this.loading === false && this.stopLoadingTricks === false) {
                this.loading = true;
                this.loadMoreTricks();
            }
        }
    };

    loadMoreTricks() {
        this.overlay.classList.remove("d-none");

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/home/' + this.page, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const html = xhr.responseText;
                    if (html.includes("post-entry-1") === false) {
                        this.stopLoadingTricks = true;
                    } else {
                        this.pageContainer.insertAdjacentHTML('beforeend', html);
                        // Ajout d'écouteurs d'événements sur les nouveaux éléments HTML
                        const newElements = this.pageContainer.querySelectorAll('.delete-tricks-' + this.page);
                        newElements.forEach(element => {
                            element.addEventListener('click', this.tricksDelete.clickHandler.bind(this.tricksDelete));
                        });
                        this.page++;
                    }
                } else {
                    console.error('Erreur lors du chargement des tricks: ', xhr.status, xhr.statusText);
                }
                setTimeout(() => {
                    this.overlay.classList.add("d-none");
                }, 500);
                this.loading = false;
            }
        };

        xhr.send();
    };
}