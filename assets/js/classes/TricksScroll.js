export default class TricksScroll
{
    constructor ()
    {
        this.page = 2;
        this.loading = false;
        this.pageContainer = document.getElementById('pages');

        //Adding listeners
        window.addEventListener('scroll', this.windowScrollListener.bind(this));

    };

    windowScrollListener () {
        if ((window.innerHeight + window.scrollY) >= (this.pageContainer.offsetTop + this.pageContainer.offsetHeight)) {
            if (this.loading === false) {
                this.loading = true;
                this.page++;
                this.loadMoreTricks(this.page);
            }
        }
    };

    loadMoreTricks(page) {
        $.ajax({
            url: '/home/' . page, 
            method: 'GET', 
            success: function(response) {
                this.pageContainer.appendChild(response);
                this.loading = false;
            }
        });
    }
}