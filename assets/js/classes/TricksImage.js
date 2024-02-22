export default class TricksImage
{
    mediaImages;
    mediaImagesFullScreen;

    constructor () {
        this.mediaImages = document.querySelectorAll('.media-image');
        this.mediaImagesFullScreen = document.querySelectorAll('.fullscreen-image');

        this.mediaImages.forEach((image) => {
            image.addEventListener('click', () => {
                const fullScreenImageId = image.getAttribute('data-image-fullscreen');
                const imageFullScreen = document.getElementById(fullScreenImageId);
                imageFullScreen.style.display = "flex"; 
            });
        });

        this.mediaImagesFullScreen.forEach((image) => {
            image.addEventListener('click', () => {
                image.style.display = "none"; 
            });
        });
    }
}