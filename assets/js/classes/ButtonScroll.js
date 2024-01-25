export default class ButtonScroll {
  constructor () {
    this.buttonScrollToPostList = document.getElementById('scroll-button');
    this.buttonScrollIcon = document.getElementById('scroll-icon');
    this.postListTop = document.getElementById('postList');

    //Adding listeners
    this.buttonScrollToPostList.addEventListener('click', this.scrollToPostListTop.bind(this));
    document.addEventListener('scroll', this.changeScrollButtonIcon.bind(this));
  }

  scrollToPostListTop () {
    this.postListTop.scrollIntoView({ behavior: 'smooth', block: 'start'});
  }

  changeScrollButtonIcon () {
    if (window.scrollY < this.postListTop.offsetTop) {
        this.buttonScrollIcon.classList.add("fa-circle-chevron-down");
        this.buttonScrollIcon.classList.remove("fa-circle-chevron-up");
    } else {
        this.buttonScrollIcon.classList.add("fa-circle-chevron-up");
        this.buttonScrollIcon.classList.remove("fa-circle-chevron-down");
    }
  }
}
