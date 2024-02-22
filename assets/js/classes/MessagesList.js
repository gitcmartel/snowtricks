export default class MessagesList {

    tricksId;
    page;
    loading;
    pageContainer;
    btnLoadMoreMessages;
    overlay;
    stopLoadingMessages;

    constructor ()
    {
        this.tricksId = document.getElementById("tricksId");
        this.page = 2;
        this.loading = false;
        this.pageContainer = document.getElementById("pages");
        this.overlay = document.getElementById("spinner-container");
        this.stopLoadingMessages = false;
        this.btnLoadMoreMessages = document.getElementById("btnLoadMoreMessages");

        //Adding listeners
        this.btnLoadMoreMessages.addEventListener('click', () => {
            if (this.loading === false && this.stopLoadingMessages === false) {
                this.loading = true;
                this.loadMoreMessages();
            }
        });
    };

    loadMoreMessages() {
        this.overlay.classList.remove("d-none");

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/message/' + this.tricksId.textContent + '/' + this.page, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const html = xhr.responseText;
                    if (html.includes("message-entry-1") === false) {
                        this.stopLoadingMessages = true;
                    } else {
                        this.pageContainer.insertAdjacentHTML('beforeend', html);
                        this.page++;
                    }
                } else {
                    console.error('Erreur lors du chargement des messages: ', xhr.status, xhr.statusText);
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